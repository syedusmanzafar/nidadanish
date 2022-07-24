<?php

use Phinx\Migration\AbstractMigration;

class AlterCpEmFeedbackSent extends AbstractMigration
{ 
    public function up()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_em_feedback_sent");
        
        $table
            ->addColumn('session_id', 'string', array('limit' => 64, 'null' => false))
            ->save();
    }

    public function down()
    {
        $options = $this->adapter->getOptions();
        $pr = $options['prefix'];

        $table = $this->table("{$pr}cp_em_feedback_sent");

        if ($table->hasColumn('session_id')) {
            $table->removeColumn('session_id');
        }
    }
}
