<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180109172713 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE genus_scientist (genus_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_66CF3FA885C4074C (genus_id), INDEX IDX_66CF3FA8A76ED395 (user_id), PRIMARY KEY(genus_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE genus_scientist ADD CONSTRAINT FK_66CF3FA885C4074C FOREIGN KEY (genus_id) REFERENCES genus (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE genus_scientist ADD CONSTRAINT FK_66CF3FA8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE genus DROP FOREIGN KEY FK_38C5106E8E9550E7');
        $this->addSql('DROP INDEX IDX_38C5106E8E9550E7 ON genus');
        $this->addSql('ALTER TABLE genus DROP genus_scientists_id');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE genus_scientist');
        $this->addSql('ALTER TABLE genus ADD genus_scientists_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE genus ADD CONSTRAINT FK_38C5106E8E9550E7 FOREIGN KEY (genus_scientists_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_38C5106E8E9550E7 ON genus (genus_scientists_id)');
    }
}