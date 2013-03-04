<?php

namespace SpoiledMilk\YoghurtBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SpoiledMilk\YoghurtBundle\Entity as Entity;

class LoadBasicData implements FixtureInterface, ContainerAwareInterface {

    private $container;

    public function load(ObjectManager $manager) {
        $this->loadFieldTypes($manager);
        $this->loadUsers($manager);
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    private function loadFieldTypes(ObjectManager $manager) {
        $ft = new Entity\FieldType();
        $ft->setName('Short text');
        $ft->setClassName('VarcharValue');
        $manager->persist($ft);
        $manager->flush();

        $ft = new Entity\FieldType();
        $ft->setName('Long text');
        $ft->setClassName('TextValue');
        $manager->persist($ft);
        $manager->flush();

        $ft = new Entity\FieldType();
        $ft->setName('Numeric');
        $ft->setClassName('NumericValue');
        $manager->persist($ft);
        $manager->flush();

        $ft = new Entity\FieldType();
        $ft->setName('Date / time');
        $ft->setClassName('DatetimeValue');
        $manager->persist($ft);
        $manager->flush();

        $ft = new Entity\FieldType();
        $ft->setName('File upload');
        $ft->setClassName('FileValue');
        $manager->persist($ft);
        $manager->flush();

        $ft = new Entity\FieldType();
        $ft->setName('Choice');
        $ft->setClassName('ChoiceValue');
        $manager->persist($ft);
        $manager->flush();

        $ft = new Entity\FieldType();
        $ft->setName('Relationship');
        $ft->setClassName('RelationshipValue');
        $manager->persist($ft);
        $manager->flush();
        
        $ft = new Entity\FieldType();
        $ft->setName('Google Map');
        $ft->setClassName('MapValue');
        $manager->persist($ft);
        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager) {
        $encoder = $this->container
                ->get('security.encoder_factory')
                ->getEncoder(new Entity\User());

        $user = new Entity\User('admin', 'pass', null, 'ROLE_ADMIN', true, 'admin@domain.com');
        $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));
        $manager->persist($user);
        $manager->flush();

        $user = new Entity\User('user1', 'pass', null, 'ROLE_USER', true, 'editor_1@domain.com');
        $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));
        $manager->persist($user);
        $manager->flush();

        $user = new Entity\User('user2', 'pass', null, 'ROLE_USER', true, 'editor_2@domain.com');
        $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));
        $manager->persist($user);
        $manager->flush();
    }

}