<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260413193946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE detalles_pedido (id INT AUTO_INCREMENT NOT NULL, cantidad INT NOT NULL, precio_unitario NUMERIC(10, 2) NOT NULL, subtotal NUMERIC(10, 2) NOT NULL, pedido_id INT NOT NULL, producto_id INT NOT NULL, INDEX IDX_DBD868FC4854653A (pedido_id), INDEX IDX_DBD868FC7645698E (producto_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE pedidos (id INT AUTO_INCREMENT NOT NULL, numero_pedido VARCHAR(20) NOT NULL, estado VARCHAR(50) NOT NULL, total NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL, usuario_id INT NOT NULL, UNIQUE INDEX UNIQ_6716CCAA9C2B05F4 (numero_pedido), INDEX IDX_6716CCAADB38439E (usuario_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE detalles_pedido ADD CONSTRAINT FK_DBD868FC4854653A FOREIGN KEY (pedido_id) REFERENCES pedidos (id)');
        $this->addSql('ALTER TABLE detalles_pedido ADD CONSTRAINT FK_DBD868FC7645698E FOREIGN KEY (producto_id) REFERENCES productos (id)');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAADB38439E FOREIGN KEY (usuario_id) REFERENCES usuarios (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detalles_pedido DROP FOREIGN KEY FK_DBD868FC4854653A');
        $this->addSql('ALTER TABLE detalles_pedido DROP FOREIGN KEY FK_DBD868FC7645698E');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAADB38439E');
        $this->addSql('DROP TABLE detalles_pedido');
        $this->addSql('DROP TABLE pedidos');
    }
}
