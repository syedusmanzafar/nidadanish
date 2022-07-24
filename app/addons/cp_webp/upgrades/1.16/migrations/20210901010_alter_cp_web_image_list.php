<?php

use Phinx\Migration\AbstractMigration;

class AlterCpWebImageList extends AbstractMigration
{ 
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_web_image_list");
        if (!$table->hasColumn('rel_image_crc')) {
            $table
                ->addColumn('rel_image_crc', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
                ->addIndex(array('rel_image_crc'))
                ->save();
        }
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_web_image_list");

        if ($table->hasColumn('rel_image_crc')) {
            $table->removeColumn('rel_image_crc');
        }
    }
}
