input {width: 100%;}
	input[type="submit"],
	input[type="button"] {
		width: auto;
	}
	#dropzone {
		min-height: 100px;
    width: 100%;
    border: 3px dashed #aaa;
    padding: 10px;
    box-sizing: border-box;
	}

.client-file { padding: 5px 8px; background-color: #efefef; color: #202020; float: left; clear: both; border: 1px solid #ccc; width: 100%; border-radius: 6px; margin-top: 10px; }
.client-file .filename {height: 30px; overflow: visible; width: 70%;}
.client-file span.name { position: relative; top: 5px; }

.dz-success-mark, 
.dz-error-mark {
	display: none;
}
.btn {padding: 6px; display: inline-block; margin-right: 6px; color: #fff; border-radius: 4px;}
.btn:last-child {margin-right: 0;  }
.btn.edit { padding: 6px 8px; background-color: #6ea4f3;}
.btn.save { padding: 6px 8px; background-color: #6ea4f3;}
.btn.add { padding: 6px 8px; background-color: #68bd7a;}
.btn.download { padding: 6px 8px; background-color: #68bd7a; }
.btn.delete { padding: 6px 8px; background-color: #d87272; }
.btn.close { padding: 6px 8px; background-color: #d87272; float: right; }

.right {float: right;}
.left {float: left;}
.none {float: none; overflow: hidden;}
.small { font-size: 0.9em; }
.half {width: 50%; padding: 0 15px; box-sizing: border-box; float: left; display: inline-block;}
.flex-row {
	display: flex;
	flex-direction: row;
}
#language-list {
	border: 1px solid #ccc;
	max-height: 400px;
	overflow-y: scroll;
	width: 330px;
	padding-right: 25px;
	padding-left: 10px;
	padding-bottom: 10px;
}

.dz-progress {
	width: 100%;
	height: 5px;
}

.dz-progress .dz-upload {
	display: block;
  width: 100%;
  height: 100%;
  background: #6ae112;
  margin-top: 10px;
}

body.dragging, body.dragging * {
  cursor: move !important;
}

.dragged {
  position: absolute;
  opacity: 0.5;
  z-index: 2000;
}

.simple_with_drop li.placeholder {
  position: relative;
  border-bottom: 1px solid #ccc;
  height: 8px;
  width: 100%;
  /** More li styles **/
}
.simple_with_drop li.placeholder:before {
  position: absolute;
  /** Define arrowhead **/
}i.fa.fa-bars {
    color: #888;
}

table.file-upload-table th,
table.file-upload-table td {
	padding-top: 6px;
	padding-bottom: 6px;
}

#upload-container {    
	padding: 10px;
  background: #f9f9f9;padding-top: 25px;
}
#upload-container .upload-file {
  padding: 10px;
  background-color: #d8eef4;
  border-radius: 6px;
  padding-left: 20px;
  margin-bottom: 15px;
}