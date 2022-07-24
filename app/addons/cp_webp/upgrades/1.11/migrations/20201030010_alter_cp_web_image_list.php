<?php

use Phinx\Migration\AbstractMigration;

class AlterCpWebImageList extends AbstractMigration
{ 
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_web_image_list");
        if (!$table->hasColumn('image_crc')) {
            $table
                ->addColumn('image_crc', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
                ->save();
        }
        if ($table->hasColumn('image_crc')) {
            $table->execute("TRUNCATE TABLE {$pr}cp_web_image_list");
            $table->execute("ALTER TABLE {$pr}cp_web_image_list DROP INDEX webp_crc, ADD UNIQUE webp_crc (image_crc) USING BTREE");
        }
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_web_image_list");

        if ($table->hasColumn('image_crc')) {
            $table->removeColumn('image_crc');
            $table->execute("TRUNCATE TABLE {$pr}cp_web_image_list");
            $table->execute("ALTER TABLE {$pr}cp_web_image_list DROP INDEX webp_crc, ADD UNIQUE webp_crc (webp_crc) USING BTREE");
        }
    }
}
