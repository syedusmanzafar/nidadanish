<?php
 use Phinx\Migration\AbstractMigration; class AddonsSdLabelsAddOnBackorderLabel extends AbstractMigration { public function up() { $options = $this->adapter->getOptions(); $label_table = $options['prefix'] . 'sd_labels'; $label_description_table = $options['prefix'] . 'sd_labels_descriptions'; $label_id = $this->getProductOnBackorderLabelId($label_table); if ($label_id === 0) { $this->execute("
                INSERT INTO {$label_table}
                    (
                        background_color,
                        text_color,
                        display_type,
                        label_type,
                        attachable,
                        position,
                        status
                    )
                VALUES(
                    'rgb(255, 0, 0)',
                    'rgb(0, 0, 0)',
                    'text',
                    'on_backorder',
                    'N',
                    8,
                    'A'
                );
            "); $label_id = $this->getProductOnBackorderLabelId($label_table); } if ($label_id > 0) { $description_data_labels = [ [ 'label_id' => $label_id, 'name' => 'On backorder', 'lang_code' => 'en', ], [ 'label_id' => $label_id, 'name' => 'Предзаказ', 'lang_code' => 'ru', ], ]; foreach ($description_data_labels as $description_data_label) { if ( !$this->existsProductOnBackorderLabelDescription( $label_description_table, $description_data_label['label_id'], $description_data_label['lang_code'] ) ) { $this->execute('
                        INSERT INTO ' . $label_description_table . '
                            (
                                label_id,
                                name,
                                lang_code
                            )
                        VALUES(
                            ' . $description_data_label['label_id'] . ',
                            \'' . $description_data_label['name'] . '\',
                            \'' . $description_data_label['lang_code'] . '\'
                        );
                    '); } } } } private function getProductOnBackorderLabelId($table) { $label = $this->fetchRow("SELECT label_id FROM {$table} WHERE label_type = 'on_backorder'"); return !empty($label['label_id']) ? (int) $label['label_id'] : 0; } private function existsProductOnBackorderLabelDescription($table, $label_id, $lang_code) { return !empty( $this->fetchRow("SELECT 1 FROM {$table} WHERE label_id = {$label_id} AND lang_code = '{$lang_code}'") ); } }