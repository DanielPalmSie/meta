DROP TABLE IF EXISTS file_chunks;

CREATE TABLE file_chunks (
                             id SERIAL PRIMARY KEY,
                             file_id VARCHAR(255),
                             chunk_index INTEGER,
                             chunk_size INTEGER,
                             chunk_data BYTEA,
                             created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_file_chunks_on_file_id_and_chunk_index ON file_chunks (file_id, chunk_index);
