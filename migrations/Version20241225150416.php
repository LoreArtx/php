<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241225150416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking CHANGE user_id user_id INT NOT NULL, CHANGE workout_session_id workout_session_id INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649FC7D03FD');
        $this->addSql('DROP INDEX IDX_8D93D649FC7D03FD ON user');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, DROP workout_sessions_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('ALTER TABLE workout_program CHANGE trainer_id trainer_id INT NOT NULL');
        $this->addSql('ALTER TABLE workout_session CHANGE program_id program_id INT NOT NULL, CHANGE trainer_id trainer_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking CHANGE user_id user_id INT DEFAULT NULL, CHANGE workout_session_id workout_session_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE workout_program CHANGE trainer_id trainer_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD workout_sessions_id INT DEFAULT NULL, DROP roles');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649FC7D03FD FOREIGN KEY (workout_sessions_id) REFERENCES workout_session (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D649FC7D03FD ON user (workout_sessions_id)');
        $this->addSql('ALTER TABLE workout_session CHANGE program_id program_id INT DEFAULT NULL, CHANGE trainer_id trainer_id INT DEFAULT NULL');
    }
}
