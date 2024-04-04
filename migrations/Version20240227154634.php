<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227154634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE checklist_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE friendship_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE notification_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE post_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rating_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE review_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE trip_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE checklist (id INT NOT NULL, trip_id INT DEFAULT NULL, participant_id INT DEFAULT NULL, task VARCHAR(100) NOT NULL, state BOOLEAN NOT NULL, property VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5C696D2FA5BC2E0E ON checklist (trip_id)');
        $this->addSql('CREATE INDEX IDX_5C696D2F9D1C3019 ON checklist (participant_id)');
        $this->addSql('COMMENT ON COLUMN checklist.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN checklist.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE friendship (id INT NOT NULL, requester_id INT NOT NULL, receiver_id INT NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, friend_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, accepted BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7234A45FED442CF4 ON friendship (requester_id)');
        $this->addSql('CREATE INDEX IDX_7234A45FCD53EDB6 ON friendship (receiver_id)');
        $this->addSql('CREATE TABLE notification (id INT NOT NULL, friendship_id INT DEFAULT NULL, recipient_id INT NOT NULL, sender_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, message VARCHAR(255) NOT NULL, is_read BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF5476CAEA7E2197 ON notification (friendship_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAE92F8F78 ON notification (recipient_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAF624B39D ON notification (sender_id)');
        $this->addSql('CREATE TABLE post (id INT NOT NULL, trip_id INT DEFAULT NULL, participant_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, image_name VARCHAR(255) DEFAULT NULL, summary TEXT NOT NULL, budget DOUBLE PRECISION DEFAULT NULL, city VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, vote INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DA5BC2E0E ON post (trip_id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D9D1C3019 ON post (participant_id)');
        $this->addSql('COMMENT ON COLUMN post.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN post.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN post.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE rating (id INT NOT NULL, participant_id INT DEFAULT NULL, post_id INT DEFAULT NULL, vote VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D88926229D1C3019 ON rating (participant_id)');
        $this->addSql('CREATE INDEX IDX_D88926224B89032C ON rating (post_id)');
        $this->addSql('CREATE TABLE review (id INT NOT NULL, trip_id INT DEFAULT NULL, participant_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, summary TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_794381C6A5BC2E0E ON review (trip_id)');
        $this->addSql('CREATE INDEX IDX_794381C69D1C3019 ON review (participant_id)');
        $this->addSql('COMMENT ON COLUMN review.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN review.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE trip (id INT NOT NULL, creator_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, budget DOUBLE PRECISION DEFAULT NULL, status VARCHAR(100) NOT NULL, country VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7656F53B61220EA6 ON trip (creator_id)');
        $this->addSql('COMMENT ON COLUMN trip.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN trip.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN trip.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE trip_tag (trip_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(trip_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_8F404E39A5BC2E0E ON trip_tag (trip_id)');
        $this->addSql('CREATE INDEX IDX_8F404E39BAD26311 ON trip_tag (tag_id)');
        $this->addSql('CREATE TABLE trip_user (trip_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(trip_id, user_id))');
        $this->addSql('CREATE INDEX IDX_A6AB4522A5BC2E0E ON trip_user (trip_id)');
        $this->addSql('CREATE INDEX IDX_A6AB4522A76ED395 ON trip_user (user_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, is_verified BOOLEAN NOT NULL, biographie TEXT DEFAULT NULL, username VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE checklist ADD CONSTRAINT FK_5C696D2FA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checklist ADD CONSTRAINT FK_5C696D2F9D1C3019 FOREIGN KEY (participant_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE friendship ADD CONSTRAINT FK_7234A45FED442CF4 FOREIGN KEY (requester_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE friendship ADD CONSTRAINT FK_7234A45FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAEA7E2197 FOREIGN KEY (friendship_id) REFERENCES friendship (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE92F8F78 FOREIGN KEY (recipient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAF624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D9D1C3019 FOREIGN KEY (participant_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D88926229D1C3019 FOREIGN KEY (participant_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D88926224B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C69D1C3019 FOREIGN KEY (participant_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B61220EA6 FOREIGN KEY (creator_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_tag ADD CONSTRAINT FK_8F404E39A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_tag ADD CONSTRAINT FK_8F404E39BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_user ADD CONSTRAINT FK_A6AB4522A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip_user ADD CONSTRAINT FK_A6AB4522A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE checklist_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE friendship_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE notification_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE post_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rating_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE review_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE trip_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE checklist DROP CONSTRAINT FK_5C696D2FA5BC2E0E');
        $this->addSql('ALTER TABLE checklist DROP CONSTRAINT FK_5C696D2F9D1C3019');
        $this->addSql('ALTER TABLE friendship DROP CONSTRAINT FK_7234A45FED442CF4');
        $this->addSql('ALTER TABLE friendship DROP CONSTRAINT FK_7234A45FCD53EDB6');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAEA7E2197');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAE92F8F78');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAF624B39D');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8DA5BC2E0E');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8D9D1C3019');
        $this->addSql('ALTER TABLE rating DROP CONSTRAINT FK_D88926229D1C3019');
        $this->addSql('ALTER TABLE rating DROP CONSTRAINT FK_D88926224B89032C');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C6A5BC2E0E');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C69D1C3019');
        $this->addSql('ALTER TABLE trip DROP CONSTRAINT FK_7656F53B61220EA6');
        $this->addSql('ALTER TABLE trip_tag DROP CONSTRAINT FK_8F404E39A5BC2E0E');
        $this->addSql('ALTER TABLE trip_tag DROP CONSTRAINT FK_8F404E39BAD26311');
        $this->addSql('ALTER TABLE trip_user DROP CONSTRAINT FK_A6AB4522A5BC2E0E');
        $this->addSql('ALTER TABLE trip_user DROP CONSTRAINT FK_A6AB4522A76ED395');
        $this->addSql('DROP TABLE checklist');
        $this->addSql('DROP TABLE friendship');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE trip');
        $this->addSql('DROP TABLE trip_tag');
        $this->addSql('DROP TABLE trip_user');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
