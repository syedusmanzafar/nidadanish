<?php

use Phinx\Migration\AbstractMigration;

class CreateCpWebImagesForChange extends AbstractMigration
{     
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table(
            "{$pr}cp_web_images_for_change",
            array('id' => false, 'engine' => 'InnoDB')
        );

        if ($table->exists()) {
            return;
        }

        $table
            ->addColumn('image_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('image_path', 'string', array('limit' => 255, 'null' => false))
            ->addColumn('webp_path', 'string', array('limit' => 255, 'null' => false))
            ->addIndex(array('image_path'), array('unique' => true,'name' => 'image_path'))
            ->addIndex(array('image_id'))
            ->create();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_web_images_for_change");

        if ($table->exists()) {
            $table->drop();
        }
    }
}
