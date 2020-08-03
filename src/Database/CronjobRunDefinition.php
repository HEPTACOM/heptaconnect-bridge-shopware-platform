<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CronjobRunDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'heptaconnect_cronjob_run';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CronjobRunEntity::class;
    }

    public function getCollectionClass(): string
    {
        return CronjobRunCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('handler', 'handler'))->addFlags(new Required()),
            new CustomFields('payload', 'payload'),
            new StringField('throwable_class', 'throwableClass'),
            new LongTextField('throwable_message', 'throwableMessage'),
            new LongTextField('throwable_serialized', 'throwableSerialized'),
            (new DateTimeField('queued_for', 'queuedFor'))->addFlags(new Required()),
            new DateTimeField('started_at', 'startedAt'),
            new DateTimeField('finished_at', 'finishedAt'),

            (new FkField('cronjob_id', 'cronjobId', CronjobDefinition::class))->addFlags(new Required()),
            new ManyToOneAssociationField('cronjob', 'cronjob_id', CronjobDefinition::class, 'id', false),

            new FkField('copy_from_id', 'copyFromId', self::class),
            new ManyToOneAssociationField('copyFrom', 'copy_from_id', self::class, 'id', false),
        ]);
    }
}
