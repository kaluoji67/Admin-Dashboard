<?php

require_once __DIR__ . '/ModelObject.class.php';

class Task extends ModelObject {
    public static function getTableName() {
        return "sqlvali_data.task";
    }

    public static function getPrefix() {
        return "tsk";
    }

    public static function getColumns()
    {
        return array(
            "tsk_id",
            "tsk_order",
            "tsk_tskg_id",
            "tsk_sem_id",
            "tsk_copyof_tsk_id"
        );
    }

    public function getPreparations() {
        if($this->getCopyofTskId()==null)
        {
            $s = TaskPreparation::getByCondition('tskp_tsk_id = ?', array($this->getId()));
        }
        else 
        {
            $s = TaskPreparation::getByCondition('tskp_tsk_id = ?', array($this->getCopyofTskId()));
        }
        return $s;
    }

    public function getPreparationsForLang($lang) {
        $where = 'tskp_tsk_id = ? and (tskp_lang is null or tskp_lang = ?)';
        
        if($this->getCopyofTskId()==null)
        {
            $s = TaskPreparation::getByCondition($where, array($this->getId(), $lang));
        }
        else
        {
            $s = TaskPreparation::getByCondition($where, array($this->getCopyofTskId(), $lang));
        }
        return $s;
    }

    public function getStatements() {
        if($this->getCopyofTskId()==null)
        {
            $s = Statement::getByCondition('stmt_tsk_id = ?', array($this->getId()));
        }
        else
        {
            $s = Statement::getByCondition('stmt_tsk_id = ?', array($this->getCopyofTskId()));
        }
        return $s;
    }

    public function getStatementsForLang($lang) {
        $where = 'stmt_tsk_id = ? and (stmt_lang is null or stmt_lang = ?)';
        if($this->getCopyofTskId()==null)
        {
            $s = Statement::getByCondition($where, array($this->getId(), $lang));
        }
        else
        {
            $s = Statement::getByCondition($where, array($this->getCopyofTskId(), $lang));
        }
        return $s;
    }

    public function getTitle($lang) {
        if($this->getCopyofTskId()==null)
        {
            $l = TaskLocalization::getByCondition('tskl_tsk_id = ? and tskl_lang = ?', array($this->getId(), $lang));
        }
        else
        {
            $l = TaskLocalization::getByCondition('tskl_tsk_id = ? and tskl_lang = ?', array($this->getCopyofTskId(), $lang));
        }
        return !empty($l) ? $l[0]->getTitle() : null;
    }

    public function getDescription($lang) {
        if($this->getCopyofTskId()==null)
        {
            $l = TaskLocalization::getByCondition('tskl_tsk_id = ? and tskl_lang = ?', array($this->getId(), $lang));
        }
        else
        {
            $l = TaskLocalization::getByCondition('tskl_tsk_id = ? and tskl_lang = ?', array($this->getCopyofTskId(), $lang));
        }
        return !empty($l) ? $l[0]->getDescription() : null;
    }
}

?>