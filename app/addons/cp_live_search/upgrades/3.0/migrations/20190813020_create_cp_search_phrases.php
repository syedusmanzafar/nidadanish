<?php

use Phinx\Migration\AbstractMigration;

class CreateCpSearchPhrases extends AbstractMigration
{
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table(
            "{$pr}cp_search_phrases",
            array('id' => false, 'primary_key' => 'phrase_id', 'engine' => 'MyISAM')
        );

        if ($table->exists()) {
            return;
        }

        $table
            ->addColumn('phrase_id', 'integer', array('signed' => false, 'null' => false, 'identity' => true))
            ->addColumn('priority', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('status', 'char', array('limit' => 1, 'null' => false, 'default' => 'D'))
            ->addColumn('company_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('lang_code', 'char', array('limit' => 2, 'null' => false))
            ->addColumn('suggestions', 'text', array('null' => false))
            ->addIndex(array('company_id', 'lang_code'), array('name' => 'company_lang'))
            ->create();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_search_phrases");

        if ($table->exists()) {
            $table->drop();
        }
    }
}
