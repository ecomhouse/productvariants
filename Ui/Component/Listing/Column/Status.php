<?php

namespace EcomHouse\ProductVariants\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column
{
    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                if ($items['status'] == 1) {
                    $items['status'] = __('Yes');
                } else {
                    $items['status'] = __('No');
                }
            }
        }
        return $dataSource;
    }
}
