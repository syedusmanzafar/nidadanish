<?php

use Phinx\Migration\AbstractMigration;

class CreateCpSearchPhraseProducts extends AbstractMigration
{
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table(
            "{$pr}cp_search_phrase_products",
            array('id' => false, 'primary_key' => array('phrase_id', 'product_id'), 'engine' => 'MyISAM')
        );

        if ($table->exists()) {
            return;
        }

        $table
            ->addColumn('phrase_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('product_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('position', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->create();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_search_phrase_products");

        if ($table->exists()) {
            $table->drop();
        }
    }
}
