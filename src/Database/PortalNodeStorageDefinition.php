<?php declare(strict_types=1);

namespace Heptacom\HeptaConnect\Bridge\ShopwarePlatform\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class PortalNodeStorageDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'heptaconnect_portal_node_storage';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return PortalNodeStorageEntity::class;
    }

    public function getCollectionClass(): string
    {
        return PortalNodeStorageCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('key', 'key', 1024))->addFlags(new Required()),
            (new LongTextField('value', 'value'))->addFlags(new Required()),
            (new StringField('type', 'type', 255))->addFlags(new Required()),

            (new FkField('portal_node_id', 'portalNodeId', PortalNodeDefinition::class))->addFlags(new Required()),
            new ManyToOneAssociationField('portalNode', 'portal_node_id', PortalNodeDefinition::class),
        ]);
    }
}
