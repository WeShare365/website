USE weshare;

INSERT INTO users (fullname, username, password, profilepic) VALUES
('John Doe', 'johndoe', '$2y$10$UO.u8UoK1t3DQ5I1Q16w/.eXt2H1ghGIdJRWKBMB1Q/1MEsjHv5Wu', 'john_doe_pic.png'),
('Jane Doe', 'janedoe', '$2y$10$UO.u8UoK1t3DQ5I1Q16w/.eXt2H1ghGIdJRWKBMB1Q/1MEsjHv5Wu', 'jane_doe_pic.png');

INSERT INTO uploads (fk_user_id, file_name) VALUES
(1, 'file1.pdf'),
(1, 'file2.docx'),
(2, 'file3.jpg');
