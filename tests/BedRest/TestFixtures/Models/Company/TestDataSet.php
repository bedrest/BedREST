<?php

namespace BedRest\TestFixtures\Models\Company;

use BedRest\TestFixtures\Models\Company\Asset as AssetEntity;
use BedRest\TestFixtures\Models\Company\Department as DepartmentEntity;
use BedRest\TestFixtures\Models\Company\Employee as EmployeeEntity;

/**
 * TestDataSet
 *
 * @author Geoff Adams <geoff@dianode.net>
 */
class TestDataSet
{
    public static function getDataSet()
    {
        $employee1 = new EmployeeEntity();
        $employee1->id = 1;
        $employee1->name = 'Jane Doe';

        $employee2 = new EmployeeEntity();
        $employee2->id = 2;
        $employee2->name = 'John Doe';

        $department1 = new DepartmentEntity();
        $department1->id = 1;
        $department1->name = 'Department #1';

        $asset1 = new AssetEntity();
        $asset1->id = 1;
        $asset1->name = 'Asset #1';

        $asset2 = new AssetEntity();
        $asset2->id = 2;
        $asset2->name = 'Asset #2';

        $asset3 = new AssetEntity();
        $asset3->id = 3;
        $asset3->name = 'Asset #3';

        $employee1->Department = $department1;
        $department1->Employees = array($employee1);

        $employee1->Assets = array($asset1, $asset2, $asset3);
        $asset1->LoanedTo = $employee1;
        $asset2->LoanedTo = $employee1;
        $asset3->LoanedTo = $employee1;

        return array(
            $employee1,
            $employee2,
            $department1,
            $asset1,
            $asset2,
            $asset3
        );
    }
}
