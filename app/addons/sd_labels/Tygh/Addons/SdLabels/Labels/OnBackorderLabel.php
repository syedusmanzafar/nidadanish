<?php
 declare(strict_types=1); namespace Tygh\Addons\SdLabels\Labels; class OnBackorderLabel extends Label { public function getHint(): ?string { return __('sd_labels.hints.on_backorder'); } } 