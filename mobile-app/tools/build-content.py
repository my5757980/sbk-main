"""Phase 1 of the SBK app: copy the website's public content into the app.

The app reads its data through src/api only. Until the website has its JSON API
(phase 2), src/api serves src/api/content.json, which this script writes from the
website's own public pages, read exactly as any visitor sees them: the stock in
the shop, each vehicle's page, the blog, and the filter lists.

    python tools/build-content.py [https://v2.sbkautotrading.com]

Nothing is submitted and nobody signs in. The fixed texts of the website (About,
Services, offices, bank accounts, ...) are not here: they live in the website's
views and are copied by hand into src/api/static.js.
"""
import html
import json
import os
import re
import sys
import time
from datetime import datetime, timezone
from urllib.parse import urljoin, urlparse

import requests
from bs4 import BeautifulSoup

SITE = (sys.argv[1] if len(sys.argv) > 1 else "https://v2.sbkautotrading.com").rstrip("/")
OUT = os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", "src", "api", "content.json")
GAP = 0.35  # seconds between requests: this is the live website

http = requests.Session()
http.headers["User-Agent"] = "Mozilla/5.0 (SBK app content build)"


def get(path):
    url = path if path.startswith("http") else SITE + path
    for attempt in range(3):
        try:
            r = http.get(url, timeout=60)
            time.sleep(GAP)
            if r.status_code == 404:
                return None
            r.raise_for_status()
            return BeautifulSoup(r.text, "lxml")
        except requests.RequestException as e:
            if attempt == 2:
                raise
            print("  retry", url, e)
            time.sleep(3)


def text(node):
    """Visible text with the website's double-escaped entities undone
    (titles are stored as 'Gray &#8211; 2024')."""
    if node is None:
        return ""
    t = node.get_text(" ", strip=True) if hasattr(node, "get_text") else str(node)
    t = html.unescape(html.unescape(t))
    t = t.replace("�", "").replace("\xa0", " ")
    return re.sub(r"\s+", " ", t).strip()


def slug_of(url):
    return urlparse(url).path.rstrip("/").split("/")[-1]


def money(s):
    m = re.search(r"[\d,]+(?:\.\d+)?", s or "")
    return float(m.group(0).replace(",", "")) if m else 0.0


def path_of(url):
    """Pictures are kept as site paths; the app puts the site address in front,
    so one build setting moves every picture with the website."""
    if not url:
        return ""
    p = urlparse(urljoin(SITE + "/", url))
    return p.path


def blocks_of(root):
    """A description written in the admin editor, as plain blocks the app can
    draw natively: headings, paragraphs, bullet lists and two-column tables."""
    out = []
    if root is None:
        return out
    for el in root.find_all(["h1", "h2", "h3", "h4", "p", "ul", "ol", "table"]):
        # Whatever sits inside a list or a table is drawn as part of it.
        if el.find_parent(["ul", "ol", "table"]) is not None:
            continue
        if el.name in ("h1", "h2", "h3", "h4"):
            t = text(el)
            if t:
                out.append({"type": "heading", "text": t})
        elif el.name == "p":
            t = text(el)
            if t:
                out.append({"type": "text", "text": t})
        elif el.name in ("ul", "ol"):
            items = [text(li) for li in el.find_all("li")]
            items = [i for i in items if i]
            if items:
                out.append({"type": "list", "items": items})
        elif el.name == "table":
            rows = []
            for tr in el.find_all("tr"):
                cells = [text(c) for c in tr.find_all(["td", "th"])]
                cells = [c for c in cells if c]
                if len(cells) >= 2 and tr.find("th") is None:
                    rows.append([cells[0], " ".join(cells[1:])])
            if rows:
                out.append({"type": "table", "rows": rows})
    # The same sentence pasted twice in a row is the editor, not the writer.
    clean = []
    for b in out:
        if clean and b == clean[-1]:
            continue
        clean.append(b)
    return clean


def shop_cards():
    cards, seen, page = [], set(), 1
    while True:
        soup = get(f"/shop?page={page}")
        found = soup.select("article.sbk-car") if soup else []
        new = 0
        for a in found:
            url = a.get("data-card-url") or (a.select_one("h3 a") or {}).get("href", "")
            slug = slug_of(url)
            if not slug or slug in seen:
                continue
            seen.add(slug)
            new += 1
            fav = a.select_one("form[action*='/wishlist/']")
            pid = int(re.search(r"/wishlist/(\d+)", fav["action"]).group(1)) if fav else None
            specs = [text(s) for s in a.select(".sbk-car__specs span")]
            cards.append({
                "id": pid,
                "slug": slug,
                "brand": a.get("data-brand") or text(a.select_one(".sbk-car__brand")),
                "model": a.get("data-model") or "",
                "title": text(a.select_one("h3")),
                "year": int(a["data-year"]) if (a.get("data-year") or "").isdigit() else None,
                "price": float(a.get("data-price") or money(text(a.select_one(".sbk-price strong")))),
                "mileageLabel": specs[1] if len(specs) > 1 else "",
                "fuel": specs[2] if len(specs) > 2 else "",
                "tag": text(a.select_one(".sbk-car__tag")),
                "image": path_of((a.select_one(".sbk-car__media img") or {}).get("src", "")),
            })
        print(f"shop page {page}: {len(found)} cards, {new} new")
        if not found or not new:
            break
        page += 1
    return cards, soup


def product(card):
    soup = get("/product/" + card["slug"])
    if soup is None:
        return None
    box = soup.select_one("section.sbk-product")
    specs = []
    for c in box.select(".sbk-spec-card"):
        label, value = text(c.find("span")), text(c.find("b"))
        if label and value:
            specs.append([label, value])
    spec = {k: v for k, v in specs}
    images = [path_of(b.get("data-image") or (b.find("img") or {}).get("src", ""))
              for b in box.select(".sbk-gallery-thumbs button")]
    if not images:
        main = box.select_one(".sbk-gallery-main img")
        images = [path_of(main.get("src"))] if main else []
    images = [i for i in images if i]
    facts = [text(s) for s in box.select(".sbk-hero-facts span")]
    kicker = text(box.select_one(".sbk-product-kicker"))
    related = [slug_of(a.get("href", "")) for a in box.select(".sbk-related-card a, .sbk-related-grid a")]
    mileage = re.sub(r"[^\d]", "", spec.get("Mileage", ""))
    return {
        **card,
        "model": card["model"] or (kicker.split("/")[-1].strip() if "/" in kicker else ""),
        "title": text(box.select_one(".sbk-product-hero h1")) or card["title"],
        "summary": text(box.select_one(".sbk-product-hero p")),
        "facts": facts,
        "location": facts[3] if len(facts) > 3 else spec.get("Location", ""),
        "mileage": int(mileage) if mileage else None,
        "transmission": spec.get("Transmission", ""),
        "bodyType": spec.get("Body Type", ""),
        "engine": spec.get("Engine", ""),
        "color": spec.get("Color", "") or spec.get("Colour", ""),
        "steering": spec.get("Steering", ""),
        "drivingType": spec.get("Driving Type", ""),
        "stockNo": spec.get("Stock No", ""),
        "chassisNo": spec.get("Chassis No", ""),
        "refNo": spec.get("Ref. No.", ""),
        "grade": spec.get("Grade", ""),
        "specs": specs,
        "images": images or ([card["image"]] if card["image"] else []),
        "description": blocks_of(box.select_one(".sbk-description-copy")),
        "related": [r for r in dict.fromkeys(related) if r and r != card["slug"]],
    }


def blogs():
    posts, seen, page = [], set(), 1
    while True:
        soup = get(f"/blog?page={page}")
        found = soup.select("article.sbk-post") if soup else []
        new = 0
        for a in found:
            link = a.select_one("h3 a")
            slug = slug_of(link.get("href", "")) if link else ""
            if not slug or slug in seen:
                continue
            seen.add(slug)
            new += 1
            meta = [text(s) for s in a.select(".sbk-meta span")]
            posts.append({
                "slug": slug,
                "title": text(link),
                "date": meta[0] if meta else "",
                "readTime": meta[1] if len(meta) > 1 else "",
                "excerpt": text(a.select_one(".sbk-post__body p")),
                "image": path_of((a.select_one(".sbk-post__img img") or {}).get("src", "")),
            })
        print(f"blog page {page}: {len(found)} posts, {new} new")
        if not found or not new:
            break
        page += 1
    for p in posts:
        soup = get("/blog/" + p["slug"])
        body = soup.select_one(".sbk-content") if soup else None
        if body is not None:
            for aside in body.select("aside"):
                aside.decompose()
        p["body"] = blocks_of(body)
    return posts


def filters(soup):
    """The shop's own filter lists, so the app offers exactly the same choices."""
    def values(name):
        box = soup.find(attrs={"name": name})
        return [text(o) for o in box.find_all("option") if o.get("value")] if box else []

    def checks(name):
        return [i.get("value") for i in soup.select(f"input[name='{name}']") if i.get("value")]

    models = []
    for o in (soup.find("select", attrs={"name": "model"}) or BeautifulSoup("", "lxml")).find_all("option"):
        if o.get("value"):
            models.append({"brand": o.get("data-brand") or text(o).split(" - ")[0], "name": o["value"]})
    return {
        "brands": checks("brands[]"),
        "types": checks("types[]"),
        "grades": checks("grades[]"),
        "models": models,
    }


def home_sections():
    soup = get("/")
    out = {}
    for sec in soup.select("section.section"):
        h2 = text(sec.find("h2"))
        slugs = [slug_of(a.get("data-card-url")) for a in sec.select("article.sbk-car[data-card-url]")]
        if h2 and slugs:
            out[h2] = slugs
    return {
        "bestSeller": out.get("This Week's Best Seller", []),
        "upcoming": out.get("The Exclusive Upcoming Collection", []),
        "newArrivals": out.get("New In-Demand Arrivals", []),
    }


def main():
    cards, last_page = shop_cards()
    first = get("/shop")
    lists = filters(first)
    products = []
    for i, c in enumerate(cards, 1):
        p = product(c)
        if p:
            products.append(p)
        if i % 10 == 0:
            print(f"vehicles {i}/{len(cards)}")
    posts = blogs()
    data = {
        "site": SITE,
        "builtAt": datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ"),
        "products": products,
        "blogs": posts,
        "filters": lists,
        "home": home_sections(),
    }
    os.makedirs(os.path.dirname(OUT), exist_ok=True)
    with open(OUT, "w", encoding="utf-8") as f:
        json.dump(data, f, ensure_ascii=False, separators=(",", ":"))
    print(f"wrote {OUT}: {len(products)} vehicles, {len(posts)} posts, "
          f"{os.path.getsize(OUT) // 1024} KB")


if __name__ == "__main__":
    main()
