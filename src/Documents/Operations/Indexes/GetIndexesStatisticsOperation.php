<?php

namespace RavenDB\Documents\Operations\Indexes;

use RavenDB\Documents\Conventions\DocumentConventions;
use RavenDB\Documents\Operations\MaintenanceOperationInterface;
use RavenDB\Http\RavenCommand;

// !status: DONE
class GetIndexesStatisticsOperation implements MaintenanceOperationInterface
{

    public function getCommand(DocumentConventions $conventions): RavenCommand
    {
        return new GetIndexesStatisticsCommand();
    }
}
