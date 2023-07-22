<?php
namespace menu\entry;

use menu\Entry;

/**
 * Хранилище элементов меню
 */
class Store {
    protected
        /**
         * Драйвер
         * @var \PDO
         */
        $pdo = null,
    
        /**
         * Имя таблицы
         * @var string
         */
        $tableName = 'menu_entry';
    
    public function __construct( \PDO $pdo ) {
        $this->pdo = $pdo;
    }
    
    /**
     * Сохранение элемента в таблицу
     * @param Entry $entry
     */
    public function Save( Entry $entry ) {
        $query = $this->pdo->prepare(
            $entry->GetId( ) ?
                'UPDATE `'.$this->tableName.'` SET `title`=:title, `name`=:name, `left_index`=:left_index, `right_index`=:right_index WHERE `id`=:id' :
                'INSERT INTO `'.$this->tableName.'`(`title`, `name`, `left_index`, `right_index`) VALUES (:title, :name, :left_index, :right_index)'
        );
        $query->bindValue( ':title', $entry->GetTitle( ) );
        $query->bindValue( ':name', $entry->GetName( ) );
        $query->bindValue( ':left_index', $entry->GetLeftIndex( ) );
        $query->bindValue( ':right_index', $entry->GetRightIndex( ) );
        
        if ( $entry->GetId( ) ) {
            $query->bindValue( ':id', $entry->GetId( ) );
        }
        
        $query->execute( );
    }
    
    /**
     * Выборка всех элементов с вычислением глубины
     * @param $limitDepth int ограничение по глубине от корня
     * @return \SplObjectStorage
     */
    public function GetWithDepth( $limitDepth = null ) {
        $query = $this->pdo->prepare(
            'SELECT node.*, (COUNT(parent.`title`) - 1) AS depth FROM `'.$this->tableName.'` AS node, `'.$this->tableName.'` AS parent '.
            'WHERE node.left_index BETWEEN parent.left_index AND parent.right_index '.
            'GROUP BY node.`title` '.( $limitDepth ? ' HAVING depth <= '.$limitDepth : '' ).' ORDER BY node.`left_index`'
        );
        $query->execute( );
        
        $ret = new \SplObjectStorage;
        
        while( $obj = $query->fetchObject( ) ) {
            $entry = new Entry;
            $entry->SetId( $obj->id );
            $entry->SetTitle( $obj->title );
            $entry->SetName( $obj->name );
            $entry->SetLeftIndex( $obj->left_index );
            $entry->SetRightIndex( $obj->right_index );
            
            $ret->attach( $entry, ( int ) $obj->depth );
        }
        
        return $ret;
    }
}
