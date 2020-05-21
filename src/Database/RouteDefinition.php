<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class RouteDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'heptaconnect_route';

    public function getEntityName(): string
    {
        return static::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return RouteEntity::class;
    }

    public function getCollectionClass(): string
    {
        return RouteCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new FkField('type_id', 'typeId', DatasetEntityTypeDefinition::class))->addFlags(new Required()),
            (new FkField('source_id', 'sourceId', PortalNodeDefinition::class))->addFlags(new Required()),
            (new FkField('target_id', 'targetId', PortalNodeDefinition::class))->addFlags(new Required()),
            (new DateTimeField('deleted_at', 'deletedAt')),

            (new ManyToOneAssociationField('type', 'type_id', DatasetEntityTypeDefinition::class, 'id')),
            (new ManyToOneAssociationField('source', 'source_id', PortalNodeDefinition::class, 'id')),
            (new ManyToOneAssociationField('target', 'target_id', PortalNodeDefinition::class, 'id')),
        ]);
    }
}
