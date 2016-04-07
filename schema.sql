CREATE TABLE results (
  business_id VARCHAR(9) NOT NULL UNIQUE,
  fetched     TIMESTAMP  NOT NULL,
  data        TEXT
);