<?php

use Phinx\Migration\AbstractMigration;

class CreateCpSearchPhraseSearchs extends AbstractMigration
{
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table(
            "{$pr}cp_search_phrase_searchs",
            array('id' => false, 'primary_key' => array('phrase_id', 'search'), 'engine' => 'MyISAM')
        );

        if ($table->exists()) {
            return;
        }

        $table
            ->addColumn('phrase_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('search', 'string', array('limit' => 256, 'null' => false))
            ->create();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_search_phrase_searchs");

        if ($table->exists()) {
            $table->drop();
        }
    }
}
