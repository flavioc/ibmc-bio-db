ALTER TABLE sequence DROP FOREIGN KEY sequence_ibfk_1;
ALTER TABLE label_sequence DROP FOREIGN KEY label_sequence_ibfk_1;
ALTER TABLE label_sequence DROP FOREIGN KEY label_sequence_ibfk_2;
ALTER TABLE label_sequence DROP FOREIGN KEY label_sequence_ibfk_3;
ALTER TABLE label_sequence DROP FOREIGN KEY label_sequence_ibfk_6;
ALTER TABLE label_sequence DROP FOREIGN KEY label_sequence_ibfk_7;
ALTER TABLE label_sequence DROP FOREIGN KEY label_sequence_ibfk_8;
ALTER TABLE sequence ENGINE=MyISAM;
ALTER TABLE label_sequence ENGINE=MyISAM;

DELIMITER //
DROP TRIGGER IF EXISTS `drop_label`//
CREATE TRIGGER `drop_label` BEFORE DELETE ON `label`
 FOR EACH ROW BEGIN
CALL DELETE_HISTORY(OLD.history_id);
DELETE FROM label_sequence WHERE label_sequence.label_id = OLD.id;
END
//

DROP TRIGGER IF EXISTS `drop_file`// 

CREATE TRIGGER `drop_file` BEFORE DELETE ON `file`
FOR EACH ROW BEGIN
DELETE FROM label_sequence WHERE label_sequence.obj_data = OLD.id;
END
//

DROP TRIGGER IF EXISTS `drop_seq`//
CREATE TRIGGER `drop_seq` BEFORE DELETE ON `sequence`
FOR EACH ROW BEGIN
CALL DELETE_HISTORY(OLD.history_id);
DELETE FROM label_sequence WHERE label_sequence.seq_id = OLD.id OR label_sequence.ref_data = OLD.id;
END
//

DROP TRIGGER IF EXISTS `drop_tax`//
CREATE TRIGGER `drop_tax` BEFORE DELETE ON `taxonomy`
 FOR EACH ROW BEGIN
   CALL DELETE_HISTORY(OLD.history_id);
     DELETE FROM label_sequence WHERE label_sequence.taxonomy_data = OLD.id;
  END
  //

DELIMITER ;
