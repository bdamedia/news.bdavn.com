angular.module("zaa").requires.push('ngAnimate');

zaa.factory('menuDndFactory', function() {
	return {
		/**
		 * variables to write
		 */
		data : {
			content: null, 
			pos:null, 
            element : null,
            offsetX: 0,
            align: '',
		},
		/**
		 * Element Getter
		 */
		getElement : function() {
			return this.data.element;
		},
		/**
		 * Elementer Setter
		 */
		setElement : function(e) {
			this.data.element = e;
		},
		/**
		 * Content Setter
		 */
		setContent : function(value) {
			this.data.content = value;
		},
		/**
		 * Content Getter
		 */
		getContent : function() {
			return this.data.content;
		},
		/**
		 * Pos Setter
		 */
		setPos: function(pos) {
			this.data.pos = pos;
		},
		/**
		 * Pos Getter
		 */
		getPos : function() {
			return this.data.pos;
        },
        /**
		 * OffsetX Setter
		 */
		setOffsetX: function(offsetX) {
			this.data.offsetX = offsetX;
		},
		/**
		 * OffsetX Getter
		 */
		getOffsetX : function() {
			return this.data.offsetX;
        },
        /**
		 * OffsetX Setter
		 */
		setAlign: function(align) {
			this.data.align = align;
		},
		/**
		 * OffsetX Getter
		 */
		getAlign : function() {
			return this.data.align;
		}
	}
});

zaa.directive('dragMenu',['menuDndFactory', 'AdminClassService', function(menuDndFactory, AdminClassService) {
	return {
		restrict : 'A',
		transclude: false,
		replace: false,
		template: false,
		templateURL: false,
		scope: {
			dndModel : '=',
			dndCss : '=',
			dndOndrop : '&',
			dndIsvalid : '&',
		},
		link: function(scope, element, attrs) {
            // In standard-compliant browsers we use a custom mime type and also encode the dnd-type in it.
			// However, IE and Edge only support a limited number of mime types. The workarounds are described
			// in https://github.com/marceljuenemann/angular-drag-and-drop-lists/wiki/Data-Transfer-Design
			var MIME_TYPE = 'application/x-dnd';
			// EDGE MIME TYPE
			var EDGE_MIME_TYPE = 'application/json';
			// IE MIME TYPE
			var MSIE_MIME_TYPE = 'Text';
			// if current droping is valid, defaults to true
			var isValid = true;
			// whether middle dropping is disabled or not
			var disableMiddleDrop = attrs.hasOwnProperty('dndDisableDragMiddle');
	        
			/* DRAGABLE */
	        
			/**
			 * Enable dragging if not disabled.
			 */
	        if (!attrs.hasOwnProperty('dndDragDisabled')) {
	        	element.attr("draggable", "true");
	        }
	        
	        /**
	         * Add a class to the current element
	         */
	        scope.addClass = function(className) {
	        	element.addClass(className);
	        };
	        
	        /**
	         * Remove a class from the current element, including timeout delay.
	         */
	        scope.removeClass = function(className, delay) {
	        	element.removeClass(className);
	        };
	
	        /**
	         * DRAG START
	         */
	        element.on('dragstart', function(e) {
	        	e = e.originalEvent || e;
	        	
	        	e.stopPropagation();
	        	
	        	// Check whether the element is draggable, since dragstart might be triggered on a child.
	            if (element.attr('draggable') == 'false') {
	            	return true;
	            }
	            
                isValid = true;
                menuDndFactory.setOffsetX(e.layerX);
            	menuDndFactory.setContent(scope.dndModel);
            	menuDndFactory.setElement(element[0]);
            	scope.addClass(scope.dndCss.onDrag);
                
                var mimeType = 'text';
                var data = "1";
                
                try {
                    e.dataTransfer.setData(mimeType, data);
                } catch (e) {
                	try {
                		e.dataTransfer.setData(EDGE_MIME_TYPE, data);
	                } catch (e) {
            			e.dataTransfer.setData(MSIE_MIME_TYPE, data);
	                }
                }
            });
	
	        /**
	         * DRAG END
	         */
	        element.on('dragend', function(e) {
                e = e.originalEvent || e;
	        	scope.removeClass(scope.dndCss.onDrag);
                e.stopPropagation();
            });
	        
	        /* DROPABLE */
	        
	        /**
	         * DRAG OVER ELEMENT
	         */
        	element.on('dragover',  function(e) {
        		e = e.originalEvent || e;
        		
        		try {
        			e.dataTransfer.dropEffect = 'move';
        		} catch(e) {
        			// catch ie exceptions
        		}
                
        		e.preventDefault();
	        	e.stopPropagation();
        		
		        if (!scope.dndIsvalid({hover: scope.dndModel, dragged: menuDndFactory.getContent()})) {
	        		isValid = false;
	        		return false;
	        	}
		        
                var re = element[0].getBoundingClientRect();
		        var height = re.height;
		        var mouseHeight = e.clientY - re.top;
                var percentage = (100 / height) * mouseHeight;
                
		        if (disableMiddleDrop) {
		        	if (percentage <= 50) {
    		        	scope.addClass(scope.dndCss.onHoverTop);
    		        	scope.removeClass(scope.dndCss.onHoverMiddle);
    		        	scope.removeClass(scope.dndCss.onHoverBottom);
    		        	menuDndFactory.setPos('top');
    		        } else {
    		        	scope.removeClass(scope.dndCss.onHoverTop);
    		        	scope.removeClass(scope.dndCss.onHoverMiddle);
    		        	scope.addClass(scope.dndCss.onHoverBottom);
    		        	menuDndFactory.setPos('bottom');
    		        }
		        } else {
		        	if (percentage <= 25) {
    		        	scope.addClass(scope.dndCss.onHoverTop);
    		        	scope.removeClass(scope.dndCss.onHoverMiddle);
    		        	scope.removeClass(scope.dndCss.onHoverBottom);
    		        	menuDndFactory.setPos('top');
    		        } else if (percentage >= 65) {
    		        	scope.removeClass(scope.dndCss.onHoverTop);
    		        	scope.removeClass(scope.dndCss.onHoverMiddle);
    		        	scope.addClass(scope.dndCss.onHoverBottom);
    		        	menuDndFactory.setPos('bottom');
    		        } else {
    		        	scope.removeClass(scope.dndCss.onHoverTop);
    		        	scope.addClass(scope.dndCss.onHoverMiddle);
    		        	scope.removeClass(scope.dndCss.onHoverBottom);
    		        	menuDndFactory.setPos('middle');
    		        }
		        }
		        
		        scope.addClass(scope.dndCss.onHover);
		        
		        return false;
		    });
        	
        	/**
        	 * DRAG ENTER element
        	 */
        	element.on('dragenter', function(e) {
        		e = e.originalEvent || e;
        		scope.addClass(scope.dndCss.onHover);
        		e.preventDefault();
		    });

        	/**
        	 * DRAG LEAVE
        	 */
    		element.on('dragleave', function(e) {
    			scope.removeClass(scope.dndCss.onHover, true);
    			scope.removeClass(scope.dndCss.onHoverTop, true);
    			scope.removeClass(scope.dndCss.onHoverMiddle, true);
    			scope.removeClass(scope.dndCss.onHoverBottom, true);
		    });

    		/**
    		 * DROP (if enabled)
    		 */
    		if (!attrs.hasOwnProperty('dndDropDisabled')) {
	            element.on('drop', function(e) {
                    e = e.originalEvent || e;
                    var mouseLeft = e.layerX - menuDndFactory.getOffsetX();
                    menuDndFactory.setAlign(mouseLeft);

	            	// The default behavior in Firefox is to interpret the dropped element as URL and
	                // forward to it. We want to prevent that even if our drop is aborted.
	                e.preventDefault();
	                e.stopPropagation();
	                
	                scope.removeClass(scope.dndCss.onHover, true);
	    			scope.removeClass(scope.dndCss.onHoverTop, true);
	    			scope.removeClass(scope.dndCss.onHoverMiddle, true);
	    			scope.removeClass(scope.dndCss.onHoverBottom, true);
	    			
    		        if (isValid) {
	                	scope.$apply(function() {
	                		scope.dndOndrop({dragged: menuDndFactory.getContent(), dropped: scope.dndModel, position: menuDndFactory.getPos(), align: menuDndFactory.getAlign(), element: menuDndFactory.getElement()});
	                	});
	                	return true;
    		        }
    		        return false;
                });
    		}
		}
	};
}]);