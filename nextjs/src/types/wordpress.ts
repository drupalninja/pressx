export interface Block {
  name: string;
  attributes: Record<string, any>;
  innerBlocks: Block[];
  innerHTML: string;
  rendered: string;
}

export interface MediaDetails {
  width: number;
  height: number;
}

export interface FeaturedImage {
  node: {
    sourceUrl: string;
    altText?: string;
    mediaDetails?: MediaDetails;
  };
}

export interface Tag {
  name: string;
  slug: string;
}

export interface Post {
  id?: string;
  title: string;
  content: string;
  excerpt?: string;
  date: string;
  databaseId: number;
  status?: string;
  blocks?: Block[];
  featuredImage?: FeaturedImage;
  tags?: {
    nodes: Tag[];
  };
}

export interface Page {
  title: string;
  content: string;
  date: string;
  databaseId: number;
  featuredImage?: FeaturedImage;
  status?: string;
  id?: string;
}

export interface Section {
  // This is a placeholder - the actual Section interface should be imported from SectionResolver
  // or defined here if it's not already defined elsewhere
  [key: string]: any;
}

export interface Landing {
  title: string;
  databaseId: number;
  sections: Section[];
}

export interface PostsResponse {
  posts: {
    nodes: Post[];
  };
}

export interface PostResponse {
  post: Post | null;
}

export interface PageResponse {
  page: Page | null;
}

export interface LandingResponse {
  landing: Landing | null;
}
