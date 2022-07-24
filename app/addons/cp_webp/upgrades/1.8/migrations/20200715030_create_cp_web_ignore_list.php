<?php

use Phinx\Migration\AbstractMigration;

class CreateCpWebIgnoreList extends AbstractMigration
{     
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table(
            "{$pr}cp_web_ignore_list",
            array('id' => false, 'primary_key' => 'image_id', 'engine' => 'InnoDB')
        );

        if ($table->exists()) {
            return;
        }

        $table
            ->addColumn('image_id', 'integer', array('signed' => false, 'null' => false, 'identity' => true))
            ->addColumn('image_path', 'string', array('limit' => 255, 'null' => false))
            ->create();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_web_ignore_list");

        if ($table->exists()) {
            $table->drop();
        }
    }
}
