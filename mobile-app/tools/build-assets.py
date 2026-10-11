"""The website's fixed pictures, made phone-sized for the app.

The website's home, About, Services and Partners pages use pictures that are
written into its views (public/assets/front/img), not managed from the admin
panel. The app carries the same pictures inside itself, so those screens open
instantly and work without a connection. Some originals are 1-2 MB PNGs; here
they are resized and saved as WebP, which looks the same on a phone.

    python tools/build-assets.py

Vehicle and blog pictures are NOT here: they come from the website as it runs.
"""
import os
import shutil
import subprocess

from PIL import Image

HERE = os.path.dirname(os.path.abspath(__file__))
SRC = os.path.normpath(os.path.join(HERE, "..", "..", "public", "assets", "front"))
OUT = os.path.normpath(os.path.join(HERE, "..", "assets", "site"))
os.makedirs(OUT, exist_ok=True)


def webp(src, name, width, quality=80):
    im = Image.open(os.path.join(SRC, "img", src))
    im = im.convert("RGBA" if im.mode in ("RGBA", "LA", "P") else "RGB")
    if im.width > width:
        im = im.resize((width, round(im.height * width / im.width)), Image.LANCZOS)
    path = os.path.join(OUT, name + ".webp")
    im.save(path, "WEBP", quality=quality, method=6)
    return path


# The website's own picture -> the app's name for it, and the width it needs.
PICTURES = {
    # home: the three hero slides
    "home-hero-prius-prime-2024.jpg": ("hero-prius", 1080),
    "home-hero-hilux-travo.webp": ("hero-hilux", 1080),
    "home-hero-santafe-2025.jpg": ("hero-santafe", 1080),
    # home: the cut-out cars (body types, auction, register, FAQ)
    "home-cutout-markx-white-2016.png": ("cut-markx-white", 640),
    "home-cutout-markx-black-2016.png": ("cut-markx-black", 640),
    "home-cutout-mercedes-gls-black-2016.png": ("cut-gls-black", 640),
    "home-cutout-honda-fit-white-2015.png": ("cut-fit-white", 640),
    "home-cutout-hilux-travo.png": ("cut-hilux", 720),
    "home-cutout-santafe-2025.png": ("cut-santafe", 720),
    "home-cutout-prius-prime-2024.png": ("cut-prius", 640),
    "home-cutout-hiace-van-2016.png": ("cut-hiace", 720),
    "hero-bmw.webp": ("body-sports", 640),
    "hero-mercedes.webp": ("body-convertible", 640),
    # home: memberships and payment cards
    "member-jct.webp": ("member-jct", 300),
    "member-at.webp": ("member-at", 300),
    "member-tvc.webp": ("member-tvc", 300),
    "pay-mastercard.webp": ("pay-mastercard", 200),
    "pay-visa.webp": ("pay-visa", 200),
    # about us
    "about-building.webp": ("about-building", 900),
    "about-owner-portrait.png": ("about-ceo", 640),
    "about-agent.webp": ("about-agent", 640),
    # services
    "services/services-sourcing.webp": ("service-sourcing", 800),
    "services/services-dealership.webp": ("service-dealership", 800),
    "services/services-customs.webp": ("service-customs", 800),
    "services/services-logistics.webp": ("service-logistics", 800),
    "services/services-parts.webp": ("service-parts", 800),
    "services/services-cta-car.webp": ("service-cta-car", 800),
    # official partners
    "partner-jdm-logo.webp": ("partner-jdm", 480),
    "partner-nazli-auto.webp": ("partner-nazli", 480),
    "partner-vasia.webp": ("partner-vasia", 480),
}

# The brand logos of the website's "Brand" menu (img/brand), plus the two
# brands in stock that the menu has no logo for (img/<brand>.png).
BRANDS = {
    "Toyota": "brand/brand-toyota.webp", "Honda": "brand/brand-honda.webp",
    "Nissan": "brand/brand-nissan.webp", "Suzuki": "brand/brand-suzuki.webp",
    "Mazda": "brand/brand-mazda.webp", "Mitsubishi": "brand/brand-mitsubishi.webp",
    "Subaru": "brand/brand-subaru.webp", "Lexus": "brand/brand-lexus.webp",
    "BMW": "brand/brand-bmw.webp", "Mercedes-Benz": "brand/brand-mercedes.webp",
    "Volkswagen": "brand/brand-volkswagen.webp", "Audi": "brand/brand-audi.webp",
    "Volvo": "brand/brand-volvo.webp", "Ford": "brand/brand-ford.webp",
    "Jeep": "brand/brand-jeep.webp", "Land Rover": "brand/brand-land-rover.webp",
    "Hyundai": "brand/brand-hyundai.webp", "Alfa Romeo": "brand/brand-alfa-romeo.webp",
    "Daihatsu": "brand/brand-daihatsu.webp", "Hino": "brand/brand-hino.webp",
    "Isuzu": "isuzu.png", "Porsche": "porsche.png",
}


def slug(name):
    return name.lower().replace(" ", "-")


def main():
    for src, (name, width) in PICTURES.items():
        webp(src, name, width)
    for brand, src in BRANDS.items():
        webp(src, "brand-" + slug(brand), 220, 85)
    shutil.copyfile(os.path.join(SRC, "img", "logo.png"), os.path.join(OUT, "logo.png"))

    # A still from each client video, for its card (the video itself plays
    # from the website).
    try:
        import imageio_ffmpeg
        ffmpeg = imageio_ffmpeg.get_ffmpeg_exe()
    except ImportError:
        ffmpeg = shutil.which("ffmpeg")
    for i in (1, 2, 3):
        video = os.path.join(SRC, "video", f"client-{i}.mp4")
        still = os.path.join(OUT, f"video-{i}.jpg")
        subprocess.run([ffmpeg, "-y", "-loglevel", "error", "-ss", "1.5", "-i", video,
                        "-frames:v", "1", "-vf", "scale=540:-2", "-q:v", "4", still], check=True)
        im = Image.open(still)
        im.save(os.path.join(OUT, f"video-{i}.webp"), "WEBP", quality=78, method=6)
        os.remove(still)

    total = sum(os.path.getsize(os.path.join(OUT, f)) for f in os.listdir(OUT))
    print(f"{len(os.listdir(OUT))} files in {OUT}, {total // 1024} KB")


if __name__ == "__main__":
    main()
