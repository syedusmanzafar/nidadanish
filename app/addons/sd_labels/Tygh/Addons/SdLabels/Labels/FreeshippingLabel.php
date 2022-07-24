<?php
 declare(strict_types=1); namespace Tygh\Addons\SdLabels\Labels; class FreeshippingLabel extends Label { public function getHint(): ?string { return __('sd_labels.hints.freeshipping'); } } 