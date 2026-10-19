# Checkout, orders, admin views và output encoding

Ngày 19-10-2026. Đọc toàn văn `checkout/index.php` (158), `checkout/success.php` (101), `orders/index.php` (93), `orders/show.php` (161), `admin/users.php` (72).

## Checkout views

`checkout/index`: helper `$escape`, `$formatMoney`, config/base URL; render errors escaped; empty/nonempty branch. Form POST hidden CSRF escaped; recipient fields/address escaped; payment selected attribute literal; product name escaped and quantity int; totals use float/0-decimal format. COD active, online explicitly disabled. `$items/$errors/$form/$total/$csrfToken` from controller.

`checkout/success`: escape + format, config, parse `NgayTao` with strtotime. Outputs receipt fields escaped, item name escaped, quantity int, money float format; dates generated with `date` after false guard. IDs in order link rawurlencode. Renders item list and table duplicating same rows (possible redundant markup). Invalid date shows dash. No escaping needed for fixed COD/literals.

## Order views

`orders/index`: escape helper, money, config. Flash text escaped; CSS class chosen from two literal strings based on exact cancelled status; order fields escaped; count cast int; order ID rawurlencode in path. `strtotime` false→dash. No pagination controls; model returns all orders.

`orders/show`: computes createdAt, canCancel exact pending comparison, status class literal. Escapes order/header/customer text, flash, product names, URL base and CSRF; quantity cast int; money formatted. Only render cancel form/dialog if pending. Form method POST and data attributes are static; action contains rawurlencode ID. JS dialog asks confirmation then requestSubmit; server independently validates. Dialog has `aria-labelledby`; keyboard/accessibility not runtime tested.

## Admin users view

`$escape` typed mixed, role/status label maps. Flash escaped. Form action/CSRF/account id/input values/search query escaped. Role/status values and labels escaped; selected literal. User strings in table and visually hidden label escaped. URL edit query value rawurlencode then whole href escaped. No password is repopulated. Status form POST with token. All identified dynamic textual/attribute sinks are escaped; numeric outputs absent.

## Output encoding conclusion for these views

Dynamic values use `htmlspecialchars(... ENT_QUOTES, UTF-8)`, numeric cast/format, literal class/selected states, or rawurlencode plus attribute escape. This is positive evidence for these five views only. Money formatting rounds to zero decimals despite DECIMAL(18,2); possible business rounding/display discrepancy. Error messages from RuntimeException can include DB product names, but views escape them.
