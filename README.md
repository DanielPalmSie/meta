# meta
# Project Launch

To launch the project, execute the following command: `sh start.sh`. The project will automatically be set up.

In your browser, enter `http://application.local/` to view the project page.

# Project Idea

The core idea of the project is to implement large file uploads using chunks. The process involves writing chunks to a folder and recording their indices in the database as the file is uploaded to the backend. In case of a connection interruption, when the user attempts to upload the same file again, the upload process resumes from the last chunk index that was stopped at.

To simulate a connection loss scenario, the system is designed to upload all chunks except the last one. The user then needs to upload the same file again for the last chunk to be uploaded, thereby completing the file assembly from its chunks.

To test this functionality, upload a file and then open the `public/uploads/to/temp/dir` folder. You will see the chunks there. Upload the same file again, and in the same folder, you will observe how the file is assembled into one piece.
