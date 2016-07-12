/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Created: 12 juil. 2016
 */

/* Create columns with auto timestamp*/
ALTER TABLE `deletions` ADD `timestamp_deleted` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `path` , 
ADD INDEX `index_timestamp_deleted` (`timestamp_deleted`);

/* Initialize old values to NULL */
UPDATE `deletions` SET `timestamp_deleted`= NULL;