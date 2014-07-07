//
//  NSString+BK.h
//  BlacK
//
//  Created by Brandon Jue on 5/31/14.
//  Copyright (c) 2014 bjued. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSString (BK)

- (BOOL)containsVariable;
- (BOOL)isVariable;
- (NSArray*)variables;

@end
