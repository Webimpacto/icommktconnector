CREATE TABLE `PREFIX_commktconnector_abandomentcarts` (
	`id_cart` INT NOT NULL,
	`send` INT NULL DEFAULT '0',
	PRIMARY KEY (`id_cart`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_commktconnector_abandomentcarts_error` (
	`id_cart` INT NOT NULL,
	`error` VARCHAR(1000) NULL DEFAULT NULL,
	PRIMARY KEY (`id_cart`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;