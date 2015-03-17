<?php
/**
 * Import Attribute Values model
 *
 * @category    Edge
 * @package     Edge_ImportAttributeOptions
 * @author      outer/edge <hello@outeredgeuk.com>
 */
class Edge_ImportAttributeOptions_Model_Import extends Mage_ImportExport_Model_Abstract
{

    /**
     * Import behavior.
     */
    const BEHAVIOR_APPEND = 'append';
    const BEHAVIOR_DELETE  = 'delete';

    /**
     * Form field names (and IDs)
     */
    const FIELD_NAME_SOURCE_FILE = 'import_file';

    /**
     * Indexes to be invalidated.
     */
     protected static $_entityInvalidatedIndexes = array (
        'catalog_product_attribute',
        'catalogsearch_fulltext',
        'catalog_product_flat',
    );

    /**
     * Invalidate indexes.
     */
    public function invalidateIndex()
    {
        $indexers = self::$_entityInvalidatedIndexes;
        foreach ($indexers as $indexer) {
            $indexProcess = Mage::getSingleton('index/indexer')->getProcessByCode($indexer);
            if ($indexProcess) {
                $indexProcess->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
            }
        }

        return $this;
    }
}

