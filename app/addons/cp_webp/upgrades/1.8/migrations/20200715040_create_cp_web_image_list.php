<?php

use Phinx\Migration\AbstractMigration;

class CreateCpWebImageList extends AbstractMigration
{     
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table(
            "{$pr}cp_web_image_list",
            array('id' => false, 'engine' => 'InnoDB')
        );

        if ($table->exists()) {
            return;
        }

        $table
            ->addColumn('webp_crc', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('webp_path', 'string', array('limit' => 255, 'null' => false))
            ->addColumn('image_path', 'string', array('limit' => 255, 'null' => false))
            ->addIndex(array('webp_crc'), array('unique' => true,'name' => 'webp_crc'))
            ->create();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_web_image_list");

        if ($table->exists()) {
            $table->drop();
        }
    }
}
