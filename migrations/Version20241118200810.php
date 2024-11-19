<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241118200810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make email unique for user table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users CHANGE email email VARCHAR(300) NOT NULL UNIQUE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users CHANGE email email VARCHAR(300) NOT NULL');
    }
}
