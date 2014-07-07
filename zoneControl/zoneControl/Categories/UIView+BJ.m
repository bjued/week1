//
//  UIView+BJ.m
//  BlacK
//
//  Created by Brandon Jue on 4/22/14.
//  Copyright (c) 2014 bjued. All rights reserved.
//

#import "UIView+BJ.h"

@implementation UIView (BJ)

- (CGFloat)x {
	return self.frame.origin.x;
}
- (CGFloat)y {
	return self.frame.origin.y;
}
- (CGFloat)width {
	return self.frame.size.width;
}
- (CGFloat)height {
	return self.frame.size.height;
}

@end
