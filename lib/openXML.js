//---- функции анализа OpenXML ----

let parser = new DOMParser()
	
function nsResolver(prefix) {
  const ns = {
		a: "http://schemas.openxmlformats.org/drawingml/2006/main",
		r: "http://schemas.openxmlformats.org/officeDocument/2006/relationships",
		p: "http://schemas.openxmlformats.org/presentationml/2006/main"
  };
  return ns[prefix] || null;
}

export function getSlideTitle (slideXML) {
	let docDOM = parser.parseFromString(slideXML, "application/xml");
	let tBlocks = docDOM.evaluate(
		'/descendant::p:sp[1]/descendant::a:t/descendant-or-self::text()',
		docDOM,
		nsResolver, 
		XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,
		null
	);
	let str = ''; 
	for (let j=0; j<tBlocks.snapshotLength; j++) {
		let tBlock = tBlocks.snapshotItem (j);
		str = str + tBlock.textContent;
	};
	if (str) {
		return str
	} else {return false}	
} 
	
export function getSlideText (slideXML) {
	let docDOM = parser.parseFromString(slideXML, "application/xml");
	let str = ''; 
	let tBlocks = docDOM.evaluate (
		'/descendant::p:sp[1]/descendant::a:p',
		docDOM,
		nsResolver,
		XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,
		null		
	);
	for (let j=0; j<tBlocks.snapshotLength; j++) {
		let tBlock = tBlocks.snapshotItem (j);
		str = str + '<h2>' + getParagraphText (docDOM, tBlock) + '</h2>';
	};
	tBlocks = docDOM.evaluate (
		'/descendant::p:sp[2]/descendant::a:p',
		docDOM,
		nsResolver,
		XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,
		null		
	);
	for (let j=0; j<tBlocks.snapshotLength; j++) {
		let tBlock = tBlocks.snapshotItem (j);
		str = str + '<p>' + getParagraphText (docDOM, tBlock) + '</p>';
	};
	str = '<div>'+str+'</div>';
	console.log (str);
	return str
}

export function getSlideNotes (noteDOM) {
	let str='';
//	note = document.createElement ('div');
	noteDOM = parser.parseFromString(noteDOM, "application/xml");
	const noteParagraphs = noteDOM.evaluate (
		'//p:sp[2]//a:p',
		noteDOM,
		nsResolver,
		XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,
		null		
	);
	for (let i=0; i<noteParagraphs.snapshotLength; i++) {
		let paragraph = noteParagraphs.snapshotItem (i);
		let tBlocks = noteDOM.evaluate (
			'descendant::a:t/descendant-or-self::text()',
			paragraph,
			nsResolver,
			XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,
			null		
		);
		for (let j=0; j<tBlocks.snapshotLength; j++) {
			let tBlock = tBlocks.snapshotItem (j);
			str = str + tBlock.textContent;
		};
		if (str) {
			str = str+'\r\n';
		}	
	}	
	return str
}

function getParagraphText (docDOM, element) {
	let tBlocks = docDOM.evaluate (
		'descendant::a:t/descendant-or-self::text()',
		element,
		nsResolver,
		XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,
		null		
	);
	let str = ''; 
	for (let j=0; j<tBlocks.snapshotLength; j++) {
		let tBlock = tBlocks.snapshotItem (j);
		str = str + tBlock.textContent;
	};
	if (str) {
//		str = '<p>'+str+'</p>';
//		console.log (str);
		return str
	} else {return false}	
}	

