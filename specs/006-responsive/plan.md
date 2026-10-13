# Plan: responsive at every width (006)

## Measuring (outside the repo, `E:\cw\sbk-resp`)
- A local copy of the website (MariaDB test data, a test CRM for CRM records) is opened in a headless browser, signed in as a visitor, a customer, an agent and the admin.
- `scan.py` opens each page once as a phone (touch, mobile viewport) and narrows/widens it from 320 to 1024px, then once as a desktop from 1024 to 2560px. At each width `measure.js` reports:
  - the page sliding sideways;
  - anything past the edge with nothing on screen clipping it;
  - content cut by its own card;
  - text spilling out of its box;
  - text on top of text;
  - stretched pictures;
  - tiny text and small buttons on phones.
- The same pass photographs every page at 320, 390, 768, 1024, 1366, 1920 and 2560px, for judging by eye.
- `probe.py` prints the CSS chain that gives an element its width.

## Fixing
- `public/assets/front/css/responsive.css` holds the fixes for the front pages. It is linked once, last, in the front layout's `<head>`. It wins against Mohsin's in-page `<style>` blocks by specificity (`html body …`) or `!important` where his rule has one.
- Admin panel fixes go in `public/assets/admin/css/responsive.css`, linked last in the admin layout.
- Our own views (`partials/crm/record`, profile, chat) are corrected in place.
- Each fix is aimed at a width range and a cause: specificity bugs, fixed sizes, nowrap text, missing breakpoints. Nothing is hidden to make a fault disappear, unless the design already hides that element at the next smaller size.

## Done when
`scan.py` reports no SIDEWAYS, WIDE, CLIP, SPILL or OVERLAP on any page, except findings judged by eye as intended (listed in tasks.md with the reason). The photographs at the seven widths are looked at again after the fixes.
