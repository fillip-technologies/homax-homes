# Search page filters – plan (nothing implemented yet)

Page: `/search` → `PropertyListingController@search` → `resources/views/search-results.blade.php`
Reference: `WhatsApp Video 2026-09-25 at 11.42.34 AM.mp4` is used only as a reference for which filter fields exist and how filters apply (multi-select, Clear all, live count). We do NOT copy its UI; ours follows the Homax site theme (navy `#000080`, white cards, Tailwind, same look as the existing search result cards).

## Today
- Filters working: `city`, `category`, `status` (project stage), `search` (text). Sort dropdown exists.
- There is no filter panel on the results page; the only way to filter is the search box on the welcome page.
- Pagination is 5 per page.

## What the reference video shows (and whether we can support it)
| Reference filter | Our data | Verdict |
|---|---|---|
| Budget | `price` is a **text** column ("75 Lakh", "1.25 Cr") | Needs a numeric column first |
| Property type (apartment, land, villa, builder floor…) | `property_type` was dropped; only `category` (Residential/Commercial) remains | Add back as `property_type` (see below) |
| BHK | `bedrooms` on `full_property_schema` + `property_details.bedrooms` / `unit_type` (one project has many units) | Yes, filter on `property_details` |
| Property size | `super_area`, `carpet_area` (also per unit) | Yes, range |
| Possession status | `project_status` (Upcoming, Pre-Launch, Early Possession, Ready to move) | Yes, already partly done |
| Possession date | `possession_date` | Yes ("within 1 / 2 / 3 yrs") |
| Furnishing status | `furnishing` | Yes |
| Amenities & facilities | `amenities`, `features` (JSON arrays; free-text additions allowed) | Yes, checkbox list built from the fixed admin options |
| RERA approved | `rera_id` not empty | Yes, one checkbox |
| Verified | `is_verified` | Yes |
| Localities | `location` / `city` / `landmark` | Yes, city plus locality (`location`) |
| Builders | `developer_name` | Yes |
| Photos & videos | `main_image`, `video_url`, `property_images` | Yes ("has video") |
| Bathrooms | `bathrooms` (also per unit) | Yes, 1+/2+/3+ |
| Floor preference / facing / posted by / project density | not stored | Skip for now |

## Recommended filters (v1)
Ordered by what buyers use most and what our data can already answer:

1. **City** (already there) and **Locality** (from `location`, dependent on city)
2. **Category** (already there) and **Property type**
3. **Budget** (min / max slider or presets)
4. **BHK** (1, 2, 3, 4, 5+) via `property_details`
5. **Possession status** (already there) plus optional possession-by date
6. **Size** (sqft range)
7. **Furnishing**
8. **Amenities** (multi-select)
9. **Builder** (`developer_name`)
10. **RERA approved**, **Verified**, **Has video** toggles
11. **Bathrooms**

Sort options stay as they are. Add "Clear all" and a live count on the button.

## Data changes
None. No migration and no new columns. Budget is parsed from the existing price text (project and unit prices) in the search request by `app/Support/PriceParser.php`. Property type is not a filter (the column was dropped earlier); category covers it.

## UI plan
- Match the existing site theme: navy `#000080` accents, white rounded cards with the same soft shadow as the result cards, Tailwind classes, existing fonts and form-control style (same as the welcome-page search bar).
- Desktop: sticky left sidebar card with collapsible groups. Mobile: a "Filters" button that opens a simple slide-in drawer/accordion in the same theme (not the reference's two-pane sheet).
- Filters are plain GET query params, so links stay shareable and pagination keeps them (`appends(request()->query())` is already used).
- Active filters shown as removable chips above the results; "Clear all" link; an "Apply" button showing the result count.
- Also fix nearby issues in `search-results.blade.php`: it reads `year_built`, which was dropped, and divides `price` (text) by area for price per sqft.

## Backend plan
- Refactor `search()` into a query builder that applies each filter only when present. Whitelist params: `city, locality, category, type[], budget_min, budget_max, bhk[], area_min, area_max, status[], furnishing[], amenities[], builder, rera, verified, video, bathrooms, sort`.
- BHK and unit price use `whereHas('details', …)`; amenities use `whereJsonContains`.
- Filter option lists (cities, localities, builders) come from active properties, like `$searchCities` on the welcome page.
- Cache option lists briefly if the table grows.
- Bump pagination to 10–12 per page.

## Build order
1. Migrations and price parsing (price_min, property_type), admin form updates, backfill.
2. Controller filter refactor with tests (`tests/Feature`).
3. Sidebar / bottom-sheet UI and chips.
4. Fix the stale fields on result cards.

## Open questions
- Is the price stored as free text on purpose (ranges, "On request")? If so, what should budget filtering do for those?
- Re-add `property_type`, or is `category` enough?
- Build all 11 filters, or start with a smaller set (city, locality, category, type, budget, BHK, status, furnishing, RERA)?
