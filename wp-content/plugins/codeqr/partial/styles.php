.feature_image {
    position: relative;
    float: left;
    border: 1px solid #ccc;
    padding: 5px;
}

.remove_image {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 20px;
    height: 20px;
    border-radius: 999px;
    background-color: #fff;
    border: 1px solid #ccc;
}

input {width: 100%;}
	input[type="submit"],
	input[type="button"] {
		width: auto;
	}
	.wide {width: 100%;}

textarea {width: 100%;}
	#dropzone {
		min-height: 100px;
    width: 100%;
    border: 3px dashed #aaa;
    padding: 10px;
    box-sizing: border-box;
	}

.confirm {
	font-size: 13px;
	color: #4cb529;
	display: block;
	width: 100%;
	clear: both;
	margin-top: 10px;
}

.client-file { padding: 8px; box-sizing: border-box; background-color: #efefef; color: #202020; float: left; clear: both; border: 1px solid #ccc; width: 100%; border-radius: 6px; margin-top: 10px; }
.client-file .filename {height: 30px; overflow: visible; width: 70%;}
.client-file span.name { position: relative; top: 5px; }
.client-file code { font-weight: 400; font-size: 12px; padding-right: 15px;}
.client-file code { font-weight: 400; font-size: 12px; padding-right: 15px;}
.client-file .flex-row {flex-direction: column; }
.client-file .flex-row > div {margin-bottom: 10px;}

.client-file span.delete {
  border: 1px solid #ccc; background-color: #fff;  color: #f00;
  padding: 4px; border-radius: 99px;
  position: absolute;
  right: -5px;
}
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
.btn.delete { padding: 6px 8px; background-color: #d87272; 
}

.grey {background-color: #efefef; }
.well {}
.well tr {padding-bottom: 30px;}
.well td {padding: 30px 20px 0;}
.well tr:last-child td {padding-bottom: 30px;}
.well th {padding: 30px 20px 0;}

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
}
i.fa.fa-bars {
    color: #888;
}

#features-list .feature {
	
}

#product-list,
#selected-products {
	padding-left: 15px;
}
h5.assoc {padding-left: 15px;}