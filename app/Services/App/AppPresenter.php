<?php

namespace App\Services\App;

use App\Models\Blog;
use App\Models\CheckoutOrder;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * What the SBK app is sent: vehicles, blogs, the customer and their orders,
 * shaped as the app reads them (mobile-app/src/api). Everything shown is what
 * the website's own pages show for the same row.
 */
class AppPresenter
{
    /** A vehicle as a card: the website's sbk-car card. */
    public static function card(Product $p): array
    {
        return [
            'id' => (int) $p->id,
            'slug' => $p->slug,
            'title' => self::clean($p->title),
            'brand' => (string) optional($p->brand)->name,
            'model' => (string) optional($p->vehicleModel)->name,
            'year' => $p->year ? (int) $p->year : null,
            'price' => (float) $p->price,
            'mileage' => $p->mileage !== null && $p->mileage !== '' ? (int) $p->mileage : null,
            'mileageLabel' => $p->mileage_label,
            'fuel' => (string) ($p->fuel_type ?? ''),
            'transmission' => (string) ($p->transmission ?? ''),
            'bodyType' => (string) ($p->body_type ?? ''),
            'type' => ProductSearch::typeLabel($p->body_type),
            'grade' => ProductSearch::grade($p),
            'tag' => (string) ($p->tag ?: ($p->is_featured ? 'Featured' : '')),
            'image' => $p->primary_image_url,
        ];
    }

    /** A vehicle's page: product/show.blade.php. */
    public static function detail(Product $p): array
    {
        $images = array_values(array_filter((array) ($p->images ?: [])));

        return self::card($p) + [
            'summary' => self::clean((string) $p->short_description),
            'location' => (string) ($p->location ?? ''),
            'engine' => (string) ($p->engine ?? ''),
            'color' => (string) ($p->color ?? ''),
            'steering' => (string) ($p->steering ?? ''),
            'drivingType' => (string) ($p->driving_type ?? ''),
            'stockNo' => (string) ($p->stock_no ?? ''),
            'chassisNo' => (string) ($p->chassis_no ?? ''),
            'refNo' => (string) ($p->ref_no ?? ''),
            'specs' => self::specs($p),
            'images' => $images
                ? array_map(fn ($image) => asset(uploadsDir('front') . $image), $images)
                : [$p->primary_image_url],
            'description' => self::blocks((string) $p->description),
        ];
    }

    /** The specification cards, in the page's order; empty ones are left out as there. */
    public static function specs(Product $p): array
    {
        $specs = [
            'Ref. No.' => $p->ref_no,
            'Brand' => optional($p->brand)->name,
            'Model' => optional($p->vehicleModel)->name,
            'Year' => $p->year,
            'Mileage' => $p->mileage_label,
            'Fuel' => $p->fuel_type,
            'Transmission' => $p->transmission,
            'Body Type' => $p->body_type,
            'Engine' => $p->engine,
            'Color' => $p->color,
            'Dimension' => $p->dimension,
            'M3' => $p->m3,
            'Steering' => $p->steering,
            'Driving Type' => $p->driving_type,
            'Location' => $p->location,
            'Stock No' => $p->stock_no,
            'Chassis No' => $p->chassis_no,
        ];
        foreach (($p->vehicle_specifications ?: []) as $specification) {
            $name = trim((string) ($specification['name'] ?? ''));
            $value = trim((string) ($specification['value'] ?? ''));
            if ($name !== '' || $value !== '') {
                $specs[$name ?: 'Specification'] = $value;
            }
        }

        $out = [];
        foreach ($specs as $label => $value) {
            if ($value) {
                $out[] = [(string) $label, self::clean((string) $value)];
            }
        }

        return $out;
    }

    /** A blog post as a card: blog/blog.blade.php. */
    public static function blogCard(Blog $b): array
    {
        $text = strip_tags($b->content ?: $b->description);

        return [
            'slug' => $b->slug,
            'title' => self::clean($b->title),
            'date' => optional($b->created_at)->format('M d, Y'),
            'readTime' => max(1, (int) ceil(str_word_count($text) / 220)) . ' min read',
            'excerpt' => self::clean(Str::limit(strip_tags($b->description ?: $b->content), 150)),
            'image' => $b->image_url,
        ];
    }

    /** A blog post: blog/show.blade.php - the description first, then the article. */
    public static function blog(Blog $b): array
    {
        $body = [];
        if ($b->description) {
            $body[] = ['type' => 'text', 'text' => self::clean(strip_tags($b->description))];
        }

        return self::blogCard($b) + [
            'body' => array_merge($body, self::blocks((string) ($b->content ?: '<p>More details will be added soon.</p>'))),
        ];
    }

    /** The signed-in customer. */
    public static function user(User $u): array
    {
        $hasPhoto = !empty($u->profile_picture) && file_exists(uploadsDir('front') . $u->profile_picture);

        return [
            'id' => (int) $u->id,
            'name' => $u->name,
            'firstName' => (string) $u->first_name,
            'email' => (string) $u->email,
            'phone' => (string) ($u->phone ?? ''),
            'country' => (string) ($u->country ?? ''),
            'photo' => $hasPhoto ? $u->profile_picture_url : null,
            'role' => (string) ($u->role ?? 'customer'),
        ];
    }

    /** A website order: the Orders tab of My Profile. */
    public static function order(CheckoutOrder $o): array
    {
        return [
            'orderNo' => $o->order_no,
            'date' => optional($o->order_date ? \Carbon\Carbon::parse($o->order_date) : $o->created_at)->format('d M Y'),
            'status' => ucfirst((string) $o->status),
            'paymentMethod' => (string) ($o->payment_method ?? ''),
            'subtotal' => (float) $o->subtotal,
            'paid' => (float) ($o->paid_amount ?? 0),
            'remaining' => (float) ($o->remaining_amount ?? max(($o->subtotal ?? 0) - ($o->paid_amount ?? 0), 0)),
            'items' => $o->items->map(function ($item) {
                $vehicle = $item->product;

                return [
                    'title' => self::clean($item->product_title),
                    'slug' => optional($vehicle)->slug,
                    'brand' => $item->brand ?: optional(optional($vehicle)->brand)->name,
                    'model' => $item->model ?: optional(optional($vehicle)->vehicleModel)->name,
                    'quantity' => (int) $item->quantity,
                    'price' => (float) $item->price,
                    'image' => $vehicle ? $vehicle->primary_image_url : asset('assets/front/img/car-markx-white.jpg'),
                    'year' => optional($vehicle)->year,
                    'mileageLabel' => $vehicle && $vehicle->mileage ? number_format($vehicle->mileage) . ' km' : null,
                    'fuel' => optional($vehicle)->fuel_type,
                ];
            })->values()->all(),
        ];
    }

    /**
     * Text as the page shows it. Titles typed into the admin editor are stored
     * HTML-escaped ("Gray &#8211; 2024"), and the page escapes them again.
     */
    public static function clean(?string $text): string
    {
        $t = html_entity_decode(html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $t = str_replace(["\u{FFFD}", "\u{00A0}"], ['', ' '], $t);

        return trim(preg_replace('/\s+/u', ' ', $t));
    }

    /**
     * A description written in the admin editor, as blocks the app draws
     * natively: headings, paragraphs, lists and two-column tables.
     */
    public static function blocks(string $html): array
    {
        if (trim($html) === '') {
            return [];
        }
        $doc = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?><div id="sbk-root">' . $html . '</div>', LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $out = [];
        $xpath = new \DOMXPath($doc);
        $nodes = $xpath->query('//div[@id="sbk-root"]//*[self::h1 or self::h2 or self::h3 or self::h4 or self::p or self::ul or self::ol or self::table]');
        foreach ($nodes as $el) {
            // Whatever sits inside a list or a table is drawn as part of it.
            for ($up = $el->parentNode; $up instanceof \DOMElement && $up->getAttribute('id') !== 'sbk-root'; $up = $up->parentNode) {
                if (in_array($up->nodeName, ['ul', 'ol', 'table'], true)) {
                    continue 2;
                }
            }
            $name = $el->nodeName;
            if (in_array($name, ['h1', 'h2', 'h3', 'h4'], true)) {
                $t = self::clean($el->textContent);
                if ($t !== '') {
                    $out[] = ['type' => 'heading', 'text' => $t];
                }
            } elseif ($name === 'p') {
                $t = self::clean($el->textContent);
                if ($t !== '') {
                    $out[] = ['type' => 'text', 'text' => $t];
                }
            } elseif ($name === 'ul' || $name === 'ol') {
                $items = [];
                foreach ($el->getElementsByTagName('li') as $li) {
                    $t = self::clean($li->textContent);
                    if ($t !== '') {
                        $items[] = $t;
                    }
                }
                if ($items) {
                    $out[] = ['type' => 'list', 'items' => $items];
                }
            } else {
                $rows = [];
                foreach ($el->getElementsByTagName('tr') as $tr) {
                    if ($tr->getElementsByTagName('th')->length) {
                        continue;
                    }
                    $cells = [];
                    foreach ($tr->getElementsByTagName('td') as $td) {
                        $t = self::clean($td->textContent);
                        if ($t !== '') {
                            $cells[] = $t;
                        }
                    }
                    if (count($cells) >= 2) {
                        $rows[] = [$cells[0], implode(' ', array_slice($cells, 1))];
                    }
                }
                if ($rows) {
                    $out[] = ['type' => 'table', 'rows' => $rows];
                }
            }
        }

        // The same block pasted twice in a row is the editor, not the writer.
        $clean = [];
        foreach ($out as $b) {
            if ($clean && end($clean) == $b) {
                continue;
            }
            $clean[] = $b;
        }

        return $clean;
    }
}
