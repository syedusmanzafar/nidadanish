<?php
 declare(strict_types=1); namespace Tygh\Addons\SdLabels\Labels; use Tygh\Database\Connection; use Tygh\Enum\YesNo; class HitLabel extends Label { public const SECONDS_IN_DAY = 60 * 60 * 24; public const SECONDS_IN_WEEK = self::SECONDS_IN_DAY * 7; public const SECONDS_IN_MONTH = self::SECONDS_IN_DAY * 30; public function getHint(): ?string { return __('sd_labels.hints.hit'); } public function cronAttachLabel(Connection $db, array $addon_settings): array { return [ 'attach_products' => $this->getHitProducts($db, $addon_settings), 'attach_langvar' => 'sd_labels.cron.label_hit_assigned', 'attach_fail_langvar' => 'sd_labels.cron.no_hit_label', ]; } public function cronRemoveLabel(Connection $db): array { $cronData = []; $cronData['remove_products'] = $this->getRemoveHitProducts($db); $cronData['remove_langvar'] = 'sd_labels.cron.label_hit_deleted'; return $cronData; } private function getHitProducts(Connection $db, array $addon_settings): array { $paid_statuses = fn_get_order_paid_statuses(); $count_of_hits = $addon_settings['cron_count_of_hits']; if ($count_of_hits === 0) { return []; } $period = 0; switch ($addon_settings['cron_hit_periods_of_counting']) { case 'period_day': $period = time() - SECONDS_IN_DAY; break; case 'period_week': $period = time() - self::SECONDS_IN_WEEK; break; case 'period_month': $period = time() - self::SECONDS_IN_MONTH; break; } $period_time = ($period === 0) ? $db->quote('') : $db->quote('AND o.timestamp > ?i', $period); $purchased_products = $db->getColumn( 'SELECT od.product_id FROM ?:order_details AS od' . ' LEFT JOIN ?:orders as o ON od.order_id = o.order_id' . ' WHERE o.status IN(?a) ?p', $paid_statuses, $period_time ); $products_count = array_count_values($purchased_products); arsort($products_count); return array_slice(array_keys($products_count), 0, $count_of_hits); } private function getRemoveHitProducts(Connection $db): array { return $db->getColumn( 'SELECT pr.product_id FROM ?:products AS pr' . ' LEFT JOIN ?:product_sd_labels as pl ON pr.product_id = pl.product_id' . ' WHERE pl.is_auto = ?s AND pl.label_id = ?s', YesNo::YES, $this->getLabelId() ); } } 