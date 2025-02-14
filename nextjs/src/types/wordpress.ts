export interface Block {
  name: string;
  attributes: Record<string, any>;
  innerBlocks: Block[];
  innerHTML: string;
  rendered: string;
}

export interface Post {
  id: string;
  title: string;
  date: string;
  excerpt: string;
  content: string;
  blocks?: Block[];
  featuredImage?: {
    node: {
      sourceUrl: string;
      altText: string;
    };
  };
}

export interface PostsResponse {
  posts: {
    nodes: Post[];
  };
}
