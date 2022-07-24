<?php

use Phinx\Migration\AbstractMigration;

class CreateCpSearchSynonyms extends AbstractMigration
{
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table(
            "{$pr}cp_search_synonyms",
            array('id' => false, 'primary_key' => 'synonym_id', 'engine' => 'MyISAM')
        );

        if ($table->exists()) {
            return;
        }

        $table
            ->addColumn('synonym_id', 'integer', array('signed' => false, 'null' => false, 'identity' => true))
            ->addColumn('value', 'string', array('limit' => 256, 'null' => false))
            ->addColumn('status', 'char', array('limit' => 1, 'null' => false, 'default' => 'D'))
            ->addColumn('company_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('lang_code', 'char', array('limit' => 2, 'null' => false))
            ->addIndex(array('company_id', 'lang_code'), array('name' => 'company_lang'))
            ->create();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_search_synonyms");

        if ($table->exists()) {
            $table->drop();
        }
    }
}
