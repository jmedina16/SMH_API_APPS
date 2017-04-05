<!DOCTYPE HTML>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="/css/ui-darkness/jquery-ui-1.9.2.custom.css" />
        <link rel="stylesheet" type="text/css" href="/css/blueimp-gallery.min.css" />
        <link rel="stylesheet" type="text/css" href="/css/jquery.fileupload-ui.css" />
        <noscript><link rel="stylesheet" type="text/css" href="/css/jquery.fileupload-ui-noscript.css"></noscript>
        <script type="text/javascript" src="/js/jquery.min.js"></script>
        <script type="text/javascript" src="/js/jquery-ui-1.9.2.custom.js"></script>
        <script type="text/javascript" src="/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/js/chnk-uploader/tmpl.min.js"></script>    
        <script type="text/javascript" src="/js/chnk-uploader/load-image.min.js"></script>    
        <script type="text/javascript" src="/js/chnk-uploader/canvas-to-blob.min.js"></script>  
        <script type="text/javascript" src="/js/chnk-uploader/jquery.blueimp-gallery.min.js"></script>     
        <script type="text/javascript" src="/js/chnk-uploader/jquery.iframe-transport.js"></script>      
        <script type="text/javascript" src="/js/chnk-uploader/jquery.fileupload.js"></script>
        <script type="text/javascript" src="/js/chnk-uploader/jquery.fileupload-process.js"></script>  
        <script type="text/javascript" src="/js/chnk-uploader/jquery.fileupload-image.js"></script>   
        <script type="text/javascript" src="/js/chnk-uploader/jquery.fileupload-audio.js"></script> 
        <script type="text/javascript" src="/js/chnk-uploader/jquery.fileupload-video.js"></script>   
        <script type="text/javascript" src="/js/chnk-uploader/jquery.fileupload-validate.js"></script>    
        <script type="text/javascript" src="/js/chnk-uploader/jquery.fileupload-ui.js" ></script> 
        <script type="text/javascript" src="http://mediaplatform.streamingmediahosting.com/html5/html5lib/v1.8.7s/resources/crypto/MD5.js"></script>  
        <script type="text/javascript" src="/js/chnk-uploader/jquery.fileupload-kaltura.js" ></script>            
        <script type="text/javascript" src="http://mediaplatform.streamingmediahosting.com/html5/html5lib/v1.8.7s/mwEmbedLoader.php"></script> 
    </head>
    <body style="background-color:#F8FAFB">
        <div class="container">
            <!-- The file upload form used as target for the file upload widget -->
            <form id="fileupload" action="//mediaplatform.streamingmediahosting.com/" method="POST" enctype="multipart/form-data">
                <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                <div class="fileupload-buttonbar">
                    <div class="col-lg-7">
                        <!-- The fileinput-button span is used to style the file input field as button -->
                        <span class="btn btn-success fileinput-button">
                            <i class="glyphicon glyphicon-plus"></i>
                            <span>Add files...</span>
                            <input  type="file" name="fileData" multiple>
                        </span>
                        <button type="submit" class="btn btn-primary start">
                            <i class="glyphicon glyphicon-upload"></i>
                            <span>Start upload</span>
                        </button>
                        <button type="reset" class="btn btn-warning cancel">
                            <i class="glyphicon glyphicon-ban-circle"></i>
                            <span>Cancel upload</span>
                        </button>
                        <button type="button" class="btn btn-danger delete">
                            <i class="glyphicon glyphicon-trash"></i>
                            <span>Delete</span>
                        </button>

                        <button type="button" class="btn btn-primary Create">
                            <i class="icon-ban-circle icon-white"></i>
                            <span>Create</span>
                        </button>
                        <input type="checkbox" class="toggle">
                        <!-- The loading indicator is shown during file processing -->
                        <span class="fileupload-loading"></span>
                    </div>
                    <!-- The global progress information -->
                    <div class="col-lg-5 fileupload-progress fade">
                        <!-- The global progress bar -->
                        <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                        </div>
                        <!-- The extended global progress information -->
                        <div class="progress-extended">&nbsp;</div>
                    </div>
                </div>
                <!-- The table listing the files available for upload/download -->
                <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
            </form>
            <br>
        </div>
        <!-- The blueimp Gallery widget -->
        <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
            <div class="slides"></div>
            <h3 class="title"></h3>
            <a class="prev">‹</a>
            <a class="next">›</a>
            <a class="close">×</a>
            <a class="play-pause"></a>
            <ol class="indicator"></ol>
        </div>
        <!-- The template to display files available for upload -->
        <script id="template-upload" type="text/x-tmpl">
            {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-upload fade">
            <td>
                <span class="preview"></span>
            </td>
            <td>
                <p class="name">{%=file.name%}</p>
                {% if (file.error) { %}
                <div><span class="label label-important">Error</span> {%=file.error%}</div>
                {% } %}
            </td>
            <td>
                <p class="size">{%=o.formatFileSize(file.size)%}</p>
                {% if (!o.files.error) { %}
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
                {% } %}
            </td>
            <td>
                {% if (!o.files.error && !i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
                {% } %}
                {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
                {% } %}
            </td>
        </tr>
        {% } %}
    </script>
    <!-- The template to display files available for download -->
    <script id="template-download" type="text/x-tmpl">
        {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-download fade">
            <td>
                <span class="preview">
                    {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                    {% } %}
                </span>
            </td>
            <td>
                <p class="name">
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                </p>
                {% if (file.error) { %}
                <div><span class="label label-important">Error</span> {%=file.error%}</div>
                {% } %}
            </td>
            <td>
                <span class="size">{%=o.formatFileSize(file.size)%}</span>
            </td>
            <td>
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                        <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
                <button class="btn  btn-primary create" data-type="{%=file.create_type%}" data-url="{%=file.create_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                        <i class="icon-ban-circle icon-white"></i>
                    <span>CREATE</span>
                </button>
                <input type="checkbox" name="create" value="1" class="toggle">
            </td>
        </tr>
        {% } %}
    </script>
    <script>
        $(document).ready(function(){
            initUpload();
        })

        function initUpload()
        {
            $('#fileupload').fileupload({
                ks: '<?php echo $_GET['ks'] ?>',
                ignoreChunkHeader:true,
                dataType: 'json',
                autoUpload: false,
                // acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,

                disableImageResize: false,
                previewMaxWidth: 100,
                previewMaxHeight: 100,
                previewCrop: true,
                maxChunkSize: 30000000
            });
        }
    </script>
</body>
</html>