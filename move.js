import fs from "fs";
try {
	fs.renameSync("IMG_9481-1-.jpg", "public/portrait.jpg");
	console.log("Moved successfully");
} catch (e) {
	console.error(e);
}
