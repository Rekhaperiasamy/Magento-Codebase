<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Pattern
 * @package Aheadworks\Csblock\Model\Source
 */
class Pattern implements OptionSourceInterface
{
    public function getOptionArray()
    {
        return [
            'every day' => __('Every day'),
            'odd days' => __('Odd days of the month'),
            'even days' => __('Even days of the month'),
            '1' => __('On 1'),
            '2' => __('On 2'),
            '3' => __('On 3'),
            '4' => __('On 4'),
            '5' => __('On 5'),
            '6' => __('On 6'),
            '7' => __('On 7'),
            '8' => __('On 8'),
            '9' => __('On 9'),
            '10' => __('On 10'),
            '11' => __('On 11'),
            '12' => __('On 12'),
            '13' => __('On 13'),
            '14' => __('On 14'),
            '15' => __('On 15'),
            '16' => __('On 16'),
            '17' => __('On 17'),
            '18' => __('On 18'),
            '19' => __('On 19'),
            '20' => __('On 20'),
            '21' => __('On 21'),
            '22' => __('On 22'),
            '23' => __('On 23'),
            '24' => __('On 24'),
            '25' => __('On 25'),
            '26' => __('On 26'),
            '27' => __('On 27'),
            '28' => __('On 28'),
            '29' => __('On 29'),
            '30' => __('On 30'),
            '31' => __('On 31'),
            'last day' => __('On the last day of month'),
            '1,11,21' => __('On 1, 11, and 21st of the month'),
            '1,11,21,31' => __('On 1, 11, 21, and 31st of the month'),
            '10,20,30' => __('On 10, 20, and 30th of the month'),
            'su' => __('On Sundays'),
            'mo' => __('On Mondays'),
            'tu' => __('On Tuesdays'),
            'we' => __('On Wednesdays'),
            'th' => __('On Thursdays'),
            'fr' => __('On Fridays'),
            'sa' => __('On Saturdays'),
            'mon-fri' => __('From Monday to Friday'),
            'sat-sun' => __('On Saturdays and Sundays'),
            'mon-sat' => __('From Monday to Saturday')
        ];
    }

    public function toOptionArray()
    {
        return [
            ['value' => 'every day',  'label' => __('Every day')],
            ['value' => 'odd days',  'label' => __('Odd days of the month')],
            ['value' => 'even days',  'label'  => __('Even days of the month')],
            ['value' => '1',  'label'  => __('On 1')],
            ['value' => '2',  'label'  => __('On 2')],
            ['value' => '3',  'label'  => __('On 3')],
            ['value' => '4',  'label'  => __('On 4')],
            ['value' => '5',  'label'  => __('On 5')],
            ['value' => '6',  'label'  => __('On 6')],
            ['value' => '7',  'label'  => __('On 7')],
            ['value' => '8',  'label'  => __('On 8')],
            ['value' => '9',  'label'  => __('On 9')],
            ['value' => '10',  'label'  => __('On 10')],
            ['value' => '11',  'label'  => __('On 11')],
            ['value' => '12',  'label'  => __('On 12')],
            ['value' => '13',  'label'  => __('On 13')],
            ['value' => '14',  'label'  => __('On 14')],
            ['value' => '15',  'label'  => __('On 15')],
            ['value' => '16',  'label'  => __('On 16')],
            ['value' => '17',  'label'  => __('On 17')],
            ['value' => '18',  'label'  => __('On 18')],
            ['value' => '19',  'label'  => __('On 19')],
            ['value' => '20',  'label'  => __('On 20')],
            ['value' => '21',  'label'  => __('On 21')],
            ['value' => '22',  'label'  => __('On 22')],
            ['value' => '23',  'label'  => __('On 23')],
            ['value' => '24',  'label'  => __('On 24')],
            ['value' => '25',  'label'  => __('On 25')],
            ['value' => '26',  'label'  => __('On 26')],
            ['value' => '27',  'label'  => __('On 27')],
            ['value' => '28',  'label'  => __('On 28')],
            ['value' => '29',  'label'  => __('On 29')],
            ['value' => '30',  'label'  => __('On 30')],
            ['value' => '31',  'label'  => __('On 31')],
            ['value' => 'last day',  'label'  => __('On the last day of month')],
            ['value' => '1,11,21',  'label'  => __('On 1, 11, and 21st of the month')],
            ['value' => '1,11,21,31',  'label'  => __('On 1, 11, 21, and 31st of the month')],
            ['value' => '10,20,30',  'label'  => __('On 10, 20, and 30th of the month')],
            ['value' => 'su',  'label'  => __('On Sundays')],
            ['value' => 'mo',  'label'  => __('On Mondays')],
            ['value' => 'tu',  'label'  => __('On Tuesdays')],
            ['value' => 'we',  'label'  => __('On Wednesdays')],
            ['value' => 'th',  'label'  => __('On Thursdays')],
            ['value' => 'fr',  'label'  => __('On Fridays')],
            ['value' => 'sa',  'label'  => __('On Saturdays')],
            ['value' => 'mon-fri',  'label'  => __('From Monday to Friday')],
            ['value' => 'sat-sun',  'label'  => __('On Saturdays and Sundays')],
            ['value' => 'mon-sat',  'label'  => __('From Monday to Saturday')],
        ];
    }

}