-- ============================================================
-- 1. EXAM (existing - shown for reference)
-- ============================================================
CREATE TABLE exam (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(50)  NOT NULL UNIQUE,
    is_active   TINYINT(1)   DEFAULT 1,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 2. MODULES (existing - shown for reference)
-- ============================================================
CREATE TABLE modules (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    exam_id     INT UNSIGNED NOT NULL,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(50)  NOT NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_modules_exam
        FOREIGN KEY (exam_id) REFERENCES exam(id)
        ON DELETE CASCADE,

    INDEX idx_modules_exam (exam_id)
);

-- ============================================================
-- 3. PTE_SECTIONS  (renamed from pte_question_types)
--    Holds the 3 PTE sections: Speaking & Writing, Reading, Listening
-- ============================================================
CREATE TABLE pte_sections (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                 VARCHAR(100) NOT NULL,          -- 'Speaking & Writing'
    tag                  CHAR(5)      NOT NULL UNIQUE,   -- 'SPWR', 'RD', 'LS'
    display_order        TINYINT UNSIGNED NOT NULL,
    time_allowed_minutes SMALLINT UNSIGNED,              -- 77, 41, 57
    description          TEXT,
    created_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- 4. PTE_MODULE
--    Bridges exam-module structure to PTE sections
-- ============================================================
CREATE TABLE pte_module (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    module_id       INT UNSIGNED NOT NULL,
    pte_section_id  INT UNSIGNED NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_ptemodule_module
        FOREIGN KEY (module_id) REFERENCES modules(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_ptemodule_section
        FOREIGN KEY (pte_section_id) REFERENCES pte_sections(id)
        ON DELETE RESTRICT,

    UNIQUE KEY uq_module_section (module_id, pte_section_id),
    INDEX idx_ptemodule_section (pte_section_id)
);

-- ============================================================
-- 5. PTE_QUESTION_SUB_TYPES  (new)
--    The 20 actual PTE question types (Read Aloud, Repeat Sentence...)
-- ============================================================
CREATE TABLE pte_question_sub_types (
    id                       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pte_section_id           INT UNSIGNED NOT NULL,
    name                     VARCHAR(100) NOT NULL,         -- 'Read Aloud'
    tag                      CHAR(5)      NOT NULL UNIQUE,  -- 'RA', 'RS', 'DI'...

    response_type            ENUM(
                                'audio_record',
                                'text_write',
                                'single_choice',
                                'multi_choice',
                                'fill_blank',
                                'reorder',
                                'highlight_words',
                                'select_option'
                              ) NOT NULL,

    stimulus_type            ENUM('text','audio','image','audio_image','none') NOT NULL,

    preparation_time_sec_default SMALLINT UNSIGNED DEFAULT 0,
    answer_time_sec_default      SMALLINT UNSIGNED,
    marks_default                TINYINT UNSIGNED,
    typical_count_in_exam        TINYINT UNSIGNED,

    instructions             TEXT,
    display_order            TINYINT UNSIGNED,
    is_active                TINYINT(1) DEFAULT 1,
    created_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_subtype_section
        FOREIGN KEY (pte_section_id) REFERENCES pte_sections(id)
        ON DELETE RESTRICT,

    INDEX idx_subtype_section (pte_section_id)
);

-- ============================================================
-- 6. PTE_QUESTION_GRANULAR  (Question Bank - core table)
-- ============================================================
CREATE TABLE pte_question_granular (
    id                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pte_sub_type_id         INT UNSIGNED NOT NULL,
    question_granular_id    VARCHAR(20)  NOT NULL UNIQUE,  -- 'RA0001', 'RS0001'

    -- Content
    question_text           TEXT,           -- main prompt / passage / image desc topic
    audio_transcript         TEXT,           -- transcript for audio-based stimulus (was question_text_2)
    audio_url                VARCHAR(500),   -- Listening, Repeat Sentence, Retell Lecture
    image_url                VARCHAR(500),   -- Describe Image, Retell Lecture
    image_alt_text           VARCHAR(255),

    -- Timing (overrides sub-type defaults if needed)
    preparation_time_sec    SMALLINT UNSIGNED,
    answer_time_sec          SMALLINT UNSIGNED,

    -- Scoring
    marks                    TINYINT UNSIGNED NOT NULL DEFAULT 1,
    correct_ans              TEXT,           -- ONLY for simple types (Write from Dictation, Answer Short Q)
    correct_ans_explanation TEXT,

    -- Writing-type constraints
    min_word_count           SMALLINT UNSIGNED,
    max_word_count           SMALLINT UNSIGNED,

    -- Metadata
    difficulty               ENUM('easy','medium','hard') DEFAULT 'medium',
    topic_tags                JSON,
    source_reference          VARCHAR(255),

    is_active                 TINYINT(1) DEFAULT 1,
    created_by                INT UNSIGNED,
    updated_by                INT UNSIGNED,
    created_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_granular_subtype
        FOREIGN KEY (pte_sub_type_id) REFERENCES pte_question_sub_types(id)
        ON DELETE RESTRICT,

    INDEX idx_granular_subtype (pte_sub_type_id),
    INDEX idx_granular_difficulty (difficulty),
    INDEX idx_granular_active (is_active)
);

-- ============================================================
-- 7. PTE_MODULE_WISE_QUESTION
--    Mapping of questions to a PTE module (mock test assembly)
-- ============================================================
CREATE TABLE pte_module_wise_question (
    id                          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pte_module_id               INT UNSIGNED NOT NULL,
    pte_question_granular_id    BIGINT UNSIGNED NOT NULL,

    display_order               SMALLINT UNSIGNED NOT NULL,
    is_active                   TINYINT(1) DEFAULT 1,
    created_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_mwq_module
        FOREIGN KEY (pte_module_id) REFERENCES pte_module(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_mwq_question
        FOREIGN KEY (pte_question_granular_id) REFERENCES pte_question_granular(id)
        ON DELETE CASCADE,

    UNIQUE KEY uq_module_question_order (pte_module_id, display_order),
    INDEX idx_mwq_question (pte_question_granular_id)
);

-- ============================================================
-- 8. QUESTION_OPTIONS  (satellite)
--    For: MCQ Single/Multiple, Select Missing Word,
--         Highlight Correct Summary, Answer Short Question
-- ============================================================
CREATE TABLE pte_question_options (
    id                       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_granular_id     BIGINT UNSIGNED NOT NULL,

    option_text              TEXT NOT NULL,
    is_correct               TINYINT(1) DEFAULT 0,
    display_order            TINYINT UNSIGNED NOT NULL,

    CONSTRAINT fk_options_question
        FOREIGN KEY (question_granular_id) REFERENCES pte_question_granular(id)
        ON DELETE CASCADE,

    INDEX idx_options_question (question_granular_id)
);

-- ============================================================
-- 9. QUESTION_BLANKS  (satellite)
--    For: R/W Fill in Blanks, Reading FIB, Listening FIB,
--         Write from Dictation
-- ============================================================
CREATE TABLE pte_question_blanks (
    id                       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_granular_id     BIGINT UNSIGNED NOT NULL,

    blank_order              TINYINT UNSIGNED NOT NULL,   -- 1, 2, 3...
    correct_answer           VARCHAR(255) NOT NULL,
    accepted_variants        JSON,        -- ["color","colour"]
    dropdown_options         JSON,        -- for R-FIB: ["although","however",...]

    CONSTRAINT fk_blanks_question
        FOREIGN KEY (question_granular_id) REFERENCES pte_question_granular(id)
        ON DELETE CASCADE,

    UNIQUE KEY uq_blank_order (question_granular_id, blank_order)
);

-- ============================================================
-- 10. QUESTION_SEGMENTS  (satellite)
--     For: Re-order Paragraphs only
-- ============================================================
CREATE TABLE pte_question_segments (
    id                       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_granular_id     BIGINT UNSIGNED NOT NULL,

    segment_text             TEXT NOT NULL,
    correct_order            TINYINT UNSIGNED NOT NULL,   -- 1 = first

    CONSTRAINT fk_segments_question
        FOREIGN KEY (question_granular_id) REFERENCES pte_question_granular(id)
        ON DELETE CASCADE,

    UNIQUE KEY uq_segment_order (question_granular_id, correct_order)
);

-- ============================================================
-- 11. QUESTION_HIGHLIGHT_WORDS  (satellite - Highlight Incorrect Words only)
-- ============================================================
CREATE TABLE pte_question_highlight_words (
    id                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_granular_id    BIGINT UNSIGNED NOT NULL,
    word_text                 VARCHAR(100) NOT NULL,
    word_order                 SMALLINT UNSIGNED NOT NULL,
    is_incorrect              TINYINT(1) DEFAULT 0,

    FOREIGN KEY (question_granular_id) REFERENCES pte_question_granular(id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    UNIQUE KEY uq_word_order (question_granular_id, word_order)
) ENGINE=InnoDB;