<?php

use Phinx\Migration\AbstractMigration;

class CreateCpWebLogImages extends AbstractMigration
{     
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table(
            "{$pr}cp_web_log_images",
            array('id' => false, 'engine' => 'InnoDB')
        );

        if ($table->exists()) {
            return;
        }

        $table
            ->addColumn('timestamp', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('webp_crc', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('log_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('webp_path', 'string', array('limit' => 255, 'null' => false))
            ->addColumn('image_crc', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('image_path', 'string', array('limit' => 255, 'null' => false))
            ->addColumn('image_size', 'decimal', array('precision' => 12, 'scale' => 3, 'signed' => false, 'null' => false))
            ->addColumn('webp_size', 'decimal', array('precision' => 12, 'scale' => 3, 'signed' => false, 'null' => false))
            ->addIndex(array('image_crc'), array('unique' => true,'name' => 'image_crc'))
            ->addIndex(array('webp_crc'))
            ->addIndex(array('log_id'))
            ->create();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_web_log_images");

        if ($table->exists()) {
            $table->drop();
        }
    }
}
