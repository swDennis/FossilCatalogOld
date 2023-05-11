CREATE TABLE IF NOT EXISTS user
(
    id       BIGINT AUTO_INCREMENT NOT NULL,
    email    VARCHAR(180)          NOT NULL,
    roles    JSON                  NOT NULL,
    password VARCHAR(255)          NOT NULL,
    UNIQUE INDEX UNIQ_user_email (email),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4
  COLLATE `utf8mb4_unicode_ci`
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS messenger_messages
(
    id           BIGINT AUTO_INCREMENT NOT NULL,
    body         LONGTEXT              NOT NULL,
    headers      LONGTEXT              NOT NULL,
    queue_name   VARCHAR(190)          NOT NULL,
    created_at   DATETIME              NOT NULL,
    available_at DATETIME              NOT NULL,
    delivered_at DATETIME DEFAULT NULL,
    INDEX IDX_messenger_messages_queue_name (queue_name),
    INDEX IDX_messenger_messages_available_at (available_at),
    INDEX IDX_messenger_messages_delivered_at (delivered_at),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4
  COLLATE `utf8mb4_unicode_ci`
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS fossil_form_field
(
    id                BIGINT AUTO_INCREMENT NOT NULL,
    fieldOrder        INT                            DEFAULT 0 NULL,
    fieldName         VARCHAR(255)          NOT NULL UNIQUE,
    fieldLabel        VARCHAR(255)          NOT NULL,
    fieldType         VARCHAR(50)           NOT NULL,
    showInOverview    BOOLEAN               NOT NULL DEFAULT false,
    allowBlank        BOOLEAN               NOT NULL DEFAULT true,
    isFilter          BOOLEAN               NOT NULL DEFAULT false,
    isRequiredDefault BOOLEAN               NOT NULL DEFAULT false,
    INDEX IDX_fossil_form_field_order (fieldOrder),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4
  COLLATE `utf8mb4_unicode_ci`
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS fossil_entity
(
    id                  BIGINT AUTO_INCREMENT NOT NULL,
    findingDate         DATE                  NULL,
    fossilNumber        VARCHAR(255)          NULL,
    fossilGenus         VARCHAR(255)          NULL,
    fossilSpecies       VARCHAR(255)          NULL,
    findingPlace        VARCHAR(255)          NULL,
    findingLayer        VARCHAR(255)          NULL,
    earthAge            VARCHAR(255)          NULL,
    descriptionAndNotes LONGTEXT              NULL,
    showInOverview      BOOLEAN               NOT NULL DEFAULT false,
    INDEX IDX_match_against_search_index (`fossilNumber`, `fossilGenus`),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4
  COLLATE `utf8mb4_unicode_ci`
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS image
(
    id                    BIGINT AUTO_INCREMENT NOT NULL,
    fossilId              BIGINT                NOT NULL,
    mimeType              VARCHAR(255)          NULL,
    imageName             VARCHAR(255)          NOT NULL,
    thumbnailName         VARCHAR(255)          NOT NULL,
    relativePath          VARCHAR(255)          NULL,
    relativeImagePath     TEXT                  NULL,
    relativeThumbnailPath TEXT                  NULL,
    absolutePath          TEXT                  NULL,
    absoluteImagePath     TEXT                  NULL,
    absoluteThumbnailPath TEXT                  NULL,
    showInGallery         BOOLEAN               NOT NULL DEFAULT false,
    isMainImage           BOOLEAN               NOT NULL DEFAULT false,
    INDEX IDX_image_fossil_id (fossilId),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4
  COLLATE `utf8mb4_unicode_ci`
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS tag
(
    id               BIGINT AUTO_INCREMENT NOT NULL,
    name             VARCHAR(255)          NOT NULL,
    isUsedAsCategory BOOLEAN               NOT NULL DEFAULT false,
    UNIQUE INDEX UNIQ_tag_name (name),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4
  COLLATE `utf8mb4_unicode_ci`
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS tag_fossil
(
    id       BIGINT AUTO_INCREMENT NOT NULL,
    tagId    BIGINT                NOT NULL,
    fossilId BIGINT                NOT NULL,
    UNIQUE INDEX UNIQ_tag_fossil_tagId_fossilId (tagId, fossilId),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4
  COLLATE `utf8mb4_unicode_ci`
  ENGINE = InnoDB;