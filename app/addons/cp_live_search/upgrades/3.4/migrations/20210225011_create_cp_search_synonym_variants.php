<?php

use Phinx\Migration\AbstractMigration;

class CreateCpSearchSynonymVariants extends AbstractMigration
{
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table(
            "{$pr}cp_search_synonym_variants",
            array('id' => false, 'primary_key' => array('synonym_id', 'variant'), 'engine' => 'MyISAM')
        );

        if ($table->exists()) {
            return;
        }

        $table
            ->addColumn('synonym_id', 'integer', array('signed' => false, 'null' => false, 'default' => 0))
            ->addColumn('variant', 'string', array('limit' => 256, 'null' => false))
            ->create();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_search_synonym_variants");

        if ($table->exists()) {
            $table->drop();
        }
    }
}
