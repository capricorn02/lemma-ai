//____ подключения к БД _____

export async function connectDB (Host, DB, User, Password) {
	let response = await fetch('/db2/connectDB.php?DB_HOST='+Host+'&DB_NAME='+DB+'&DB_USER='+User+'&DB_PASSWORD='+Password);	
	if (response.ok) { 
 	return	await response.text();
	} else {
		false;
	}
}	

export async function reconnectDB () {
	let response = await fetch('/db2/reconnectDB.php');	
	if (response.ok) { 
		return await response.text();		
	} else {
		false;
	}
}
//____ type ____
export async function getDemoType (ID) {
	let response = await fetch('/db2/getDemoType.php?ID='+ID);	
	if (response.ok) { 
		return await response.json();		
	} else {
		false;
	}
}

//____ card ____

export async function newCard (title) {
	let formData = new FormData();
	formData.append('Title', title);
	let response = await fetch('/db2/newCard.php', {
		method: 'POST',
		body: formData
	});
	if (response.ok) {
		return await response.json();
	} else {
		return false;
	}
}
 
export async function setCard (card) {
	let formData = new FormData();
	formData.append('Card', JSON.stringify (card));	
	let response = await fetch('/db2/setCard.php', {
		method: 'POST',
		body: formData
	});
	if (response.ok) {
		return await response.text();
	} else {
		return 'error';
	}
}

export async function getCard (ID) {
	let response = await fetch('/db2/getCard.php?ID='+ID);	
	if (response.ok) { 
		return await response.json();		
	} else {
		false;
	}
}

export async function setCardBody (ID, blob) {
	let formData = new FormData();
	formData.append('ID', ID);
	formData.append('Body', blob, 'body.dat');
	let response = await fetch('/db2/setCardBody.php', {
		method: 'POST',
		body: formData
	});
	if (await response.ok) {
		return true;
	} else {
		return false;
	}
}

export async function getCardBody (ID) {
	let response = await fetch('/db2/getCardBody.php?ID=' + ID);
	if (await response.ok) {
		xxx = await response.text()
		console.log (xxx);
		return true;
	} else {
		return false;
	}
}

//____ folder ____
export async function newFolder (title) {
	let formData = new FormData();
	formData.append('Title', title);
	let response = await fetch('/db2/newFolder.php', {
		method: 'POST',
		body: formData
	});
	if (response.ok) {
		return await response.json();
	} else {
		return false;
	}
}
 
export async function setFolder (folder) {
	let formData = new FormData();
	formData.append('Folder', JSON.stringify (folder));	
	let response = await fetch('/db2/setFolder.php', {
		method: 'POST',
		body: formData
	});
	if (response.ok) {
		return await response.text();
	} else {
		return 'error';
	}
}

export async function getFolderList () {
	let response = await fetch('/db2/getFolderList.php');	
	if (response.ok) { 
		return await response.json();		
	} else {
		false;
	}
}

export async function getFolder (ID) {
	let response = await fetch('/db2/getFolder.php?ID='+ID);	
	if (response.ok) { 
		return await response.json();		
	} else {
		false;
	}
}

export async function getFolderCards (ID) {
	let response = await fetch('/db2/getFolderCards.php?ID='+ID);	
	if (response.ok) {
		sectionArr = await response.json();
		return sectionArr;
	} else {
		return false;
	}
}

export async function newShortcut (cardID, folderID) {
	let formData = new FormData();
	formData.append('CardID', cardID);
	formData.append('FolderID', folderID);	
	let response = await fetch('/db2/newShortcut.php', {
		method: 'POST',
		body: formData
	});
	if (response.ok) {
	let ID = await response.text();
	console.log (ID);		
		return ID;
	} else {
		return false;
	}
} 

//____ slide ____

export async function newSlide (slide) {
	let formData = new FormData();
	formData.append('Slide', JSON.stringify(slide));
	let response = await fetch('/db2/newSlide.php', {
		method: 'POST',
		body: formData
	});
	if (response.ok) {
	let ID = await response.text();
	console.log (ID);		
		return ID;
	} else {
		return false;
	}
} 

export async function getSlide (ID) {
	let response = await fetch('/db2/getSlide.php?ID='+ID);	
	if (response.ok) { 
		return await response.json();		
	} else {
		false;
	}
}

export async function getSlideBody (ID) {
	let response = await fetch('/db2/getSlideBody.php?ID='+ID);	
	if (response.ok) { 
		return await response.blob();		
	} else {
		false;
	}
}

export async function setSlide (slide) {
	let formData = new FormData();
	formData.append('Slide', JSON.stringify(slide));
	let response = await fetch('/db2/setSlide.php', {
		method: 'POST',
		body: formData
	});
	if (await response.ok) {
	xxx = await response.text()
	console.log (xxx);
		return true;
	} else {
		return false;
	}
}

async function setSlideBody (ID, blob) {
	let formData = new FormData();
	formData.append('ID', ID);
	formData.append('Body', blob, 'body.dat');

	let response = await fetch('/db/setSlideBody.php', {
		method: 'POST',
		body: formData
	});
	if (await response.ok) {
		xxx = await response.text()
		console.log (xxx);
		return true;
	} else {
		return false;
	}
}

//____ scenario ____
export async function newScenario (title) {
	let formData = new FormData();
	formData.append('Title', title);
	let response = await fetch('/db2/newScenario.php', {
		method: 'POST',
		body: formData
	});
	if (response.ok) {
		return await response.json();
	} else {
		return false;
	}
}

export async function setScenario (scenario) {
	let formData = new FormData();
	formData.append('Scenario', JSON.stringify (scenario));	
	let response = await fetch('/db2/setScenario.php', {
		method: 'POST',
		body: formData
	});
	if (response.ok) {
		return await response.text();
	} else {
		return 'error';
	}
} 

export async function getScenario (ID) {
	let response = await fetch('/db2/getScenario.php?ID='+ID);	
	if (response.ok) { 
		return await response.json();		
	} else {
		false;
	}
}

export async function delScenario (ID) {
	let response = await fetch('/db2/delScenario.php?ID=' + ID)
	if (!response.ok) return false
	let xxx = await response.text()
	console.log (xxx)
	if (xxx) {
		return true
	} else {
		return false
	}
}

export async function getScenarioList () {
	let response = await fetch('/db2/getScenarioList.php');	
	if (response.ok) { 
		return await response.json();		
	} else {
		false;
	}
}

export async function getScenarioSlides (ID) {
	let response = await fetch('/db2/getScenarioSlides.php?ID='+ID);	
	if (response.ok) {
		let arr = await response.json();
		return arr;
	} else {
		return false;
	}
}

export async function addCard2Scenario (cardID, scenarioID) {
	let formData = new FormData();
	formData.append('CardID', cardID);
	formData.append('ScenarioID', scenarioID);	
	let response = await fetch('/db2/addCard2Scenario.php', {
		method: 'POST',
		body: formData
	});
	if (response.ok) {
	let ID = await response.text();
//	console.log (ID);		
		return ID;
	} else {
		return false;
	}
} 

export async function newLecture (title) {
	let formData = new FormData();
	formData.append('Title', title);
	let response = await fetch('/db2/newLecture.php', {
		method: 'POST',
		body: formData
	});
	if (response.ok) {
		return await response.json();
	} else {
		return false;
	}
}

export async function getLectureList () {
	let response = await fetch('/db2/getLectureList.php');	
	if (response.ok) { 
		return await response.json();		
	} else {
		false;
	}
}

export async function getLecture (ID) {
	let response = await fetch('/db2/getLecture.php?ID='+ID);	
	if (response.ok) { 
		return await response.json();		
	} else {
		false;
	}
}

export async function setLecture (lecture) {
	let formData = new FormData();
	formData.append('Lecture', JSON.stringify(lecture));
	let response = await fetch('/db2/setLecture.php', {
		method: 'POST',
		body: formData
	});
	if (await response.ok) {
//	xxx = await response.text()
//	console.log (xxx);
		return true;
	} else {
		return false;
	}
}

export async function delLecture (ID) {
	let response = await fetch('/db2/delLecture.php?ID=' + ID)
	if (!response.ok) return false
	let xxx = await response.text()
	console.log (xxx)
	if (xxx) {
		return true
	} else {
		return false
	}
}

//____ Section ____
export async function newSection (section) {
	let formData = new FormData();
	formData.append('Section', JSON.stringify(section));
	let response = await fetch('/db/newSection.php', {
		method: 'POST',
		body: formData
	});
	if (response.ok) {
	let ID = await response.text();
	console.log (ID);		
		return ID;
	} else {
		return false;
	}
} 

export async function getSection (ID) {
	let response = await fetch('/db2/getSection.php?ID='+ID);	
	if (response.ok) { 
		return await response.json();
	} else {
		false;
	}
}

export async function getSectionBody (ID) {
	let response = await fetch('/db2/getSectionBody.php?ID='+ID);	
	if (response.ok) { 
		return await response.blob();		
	} else {
		false;
	}
}

export async function setSection (section) {
	let formData = new FormData();
	formData.append('Section', JSON.stringify(section));
	let response = await fetch('/db2/setSlide.php', {
		method: 'POST',
		body: formData
	});
	if (await response.ok) {
		return true;
	} else {
		return false;
	}
}

export async function getLectureSections (ID) {
	let response = await fetch('/db2/getLectureSections.php?ID='+ID);	
	if (response.ok) {
		return await response.json()
	} else {
		return false;
	}
}
/*
async function delLectureSection (ID) {
	let response = await fetch('/db/delLectureSection.php?ID=' + ID);
	if (response.ok) {
		console.log (await response.text());
		return true;
	} else {
		return false;
	}
} */

export async function setSectionQuiz (ID, data) {
	let formData = new FormData();
	formData.append('ID', ID);
	formData.append('Quiz', JSON.stringify(data));

	let response = await fetch('/db/setSectionQuiz.php', {
		method: 'POST',
		body: formData
	});
	if (await response.ok) {
	xxx = await response.text()
	console.log (xxx);
		return true;
	} else {
		return false;
	}
}

export async function setSectionBody (ID, blob) {
	let formData = new FormData();
	formData.append('ID', ID);
	formData.append('Body', blob, 'body.dat');

	let response = await fetch('/db/setSectionBody.php', {
		method: 'POST',
		body: formData
	});
	if (await response.ok) {
		return true;
	} else {
		return false;
	}
}

export async function setSectionVideo (ID, blob) {
	let formData = new FormData();
	formData.append('ID', ID);
	formData.append('Video', blob, 'video.dat');

	let response = await fetch('/db2/setSectionVideo.php', {
		method: 'POST',
		body: formData
	});
	if (await response.ok) {
		return true;
	} else {
		return false;
	}
}

export async function setSectionControls (ID, data) {
	let formData = new FormData();
	formData.append('ID', ID);
	formData.append('Controls', JSON.stringify(data));

	let response = await fetch('/db2/setSectionControls.php', {
		method: 'POST',
		body: formData
	});
	if (await response.ok) {
		return true;
	} else {
		return false;
	}
}

export async function addLectureSection (lectureID, title) {
	let formData = new FormData();
	formData.append('ID', lectureID);
	formData.append('Title', title);
	let response = await fetch('/db2/addLectureSection.php', {
		method: 'POST',
		body: formData
	});
	if (response.ok) {
	let section = await response.json();
		return section;
	} else {
		return false;
	}
} 


//____ Student ____
export async function isStudentLoginFree (login) {
	let response = await fetch('/db2/isStudentLoginFree.php?Login='+login);	
	if (response.ok) {
		return true
	} else {
		return false
	}
}

/*
export async function newStudent (slide) {
	let formData = new FormData();
	formData.append('Student', JSON.stringify(slide));
	let response = await fetch('/db2/newStudent.php', {
		method: 'POST',
		body: formData
	});
	if (response.ok) {
	let ID = await response.text();
	console.log (ID);		
		return ID;
	} else {
		return false;
	}
}*/

export async function newStudent (login, password, lastName, firstName) {
	let formData = new FormData();
	formData.append('Login', login);
	formData.append('Password', password);
	formData.append('Last_Name', lastName);
	formData.append('First_Name', firstName);
	let response = await fetch('/db2/newSudent.php', {
		method: 'POST',
		body: formData
	});
	if (await response.ok) {
		console.log (await response.text());
		return true;		
	} else {
		return false;
	}
}	

export async function getStudents () {
	let response = await fetch('/db2/getStudents.php');	
	if (response.ok) { 
		return await response.json();		
	} else {
		false;
	}
}

export async function getStudent (login, password) {
	let response = await fetch(`/db2/getStudent.php?Login=${login}&Password=${password}`);	
	if (response.ok) { 
	return await response.json()		
	} else {
		false
	}
}

/* 
export async function getStudent (login) {
	let response = await fetch('/db2/getStudent.php?Login='+login);	
	if (response.ok) { 
		let student = await response.json();
		return student;
	} else {
		return false;
	}
}*/

export async function setStudentBody (ID, blob) {
	let formData = new FormData();
	formData.append('Login', ID);
	formData.append('Body', blob, 'body.dat');

	let response = await fetch('/db2/setStudentBody.php', {
		method: 'POST',
		body: formData
	});
	if (await response.ok) {
		xxx = await response.text()
		console.log (xxx);
		return true;
	} else {
		return false;
	}
}

export async function setStudentDescriptor (login, descriptor) { 
	let arr=[];
	for (let i=0; i<128; i++) {arr[i] = descriptor[i];} 
	let formData = new FormData();
	formData.append('Login', login);
	formData.append('Descriptor', JSON.stringify(arr));
	let response = await fetch('/db2/setStudentDescriptor.php', {
		method: 'POST',
		body: formData
	});
	if (await response.ok) {
		console.log (await response.text());
		return true;		
	} else {
		return false;
	}
}

export async function getStudentDescriptor (login) {
	let response = await fetch('/db2/getStudentDescriptor.php?Login=' + login);	
	if (response.ok) { 
		let arr = await response.json();
		let descriptor = new Float32Array(128);
		for (let i=0; i<128; i++) {descriptor[i] = arr[i]}  
		return descriptor;
	} else {
		return false;
	}
}

//____ Ingress & Results ____	
export async function getIngress (ID, login) {
	response = await fetch('/db2/getIngress.php?ID=' + ID + '&Login=' + login);	
	if (response.ok) { 
		ingress = await response.json();
		return ingress;
	} else {
		return false;
	}
}

export async function getResults (ID, login) {
	let response = await fetch('/db2/getResults.php?ID=' + ID + '&Login=' + login);	
	if (response.ok) { 
		return await response.json()		 
	} else {
		return false;
	}
}

export async function setResults (ID, Login, Results) {
	let formData = new FormData();
	formData.append('Login', Login);
	formData.append('ID', ID);
	formData.append('Results', JSON.stringify (Results));	

	let response = await fetch('/db2/setResults.php', {
		method: 'POST',
		body: formData
	});
	if (response.ok) {
		console.log (await response.text());
		return true;
	} else {
		return false;
	}
}
