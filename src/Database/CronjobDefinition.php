<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CronjobDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'heptaconnect_cronjob';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CronjobEntity::class;
    }

    public function getCollectionClass(): string
    {
        return CronjobCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('cron_expression', 'cronExpression'))->addFlags(new Required()),
            (new StringField('handler', 'handler'))->addFlags(new Required()),
            (new DateTimeField('queued_until', 'queuedUntil'))->addFlags(new Required()),
            new CustomFields('payload', 'payload'),
        ]);
    }
}
