<?php


return [

    "max_cronjob_application" => env("MAX_CRONJOB_APPLICATION", 10),

    "fetch_access_log_limit" => env("FETCH_ACCESS_LOG_LIMIT", 3000),

    "insert_chunk_size" => env("INSERT_CHUNK_SIZE", 50),

    "process_chunk_read_size" => (int)env("PROCESS_CHUNK_READ_SIZE", 1000)
];